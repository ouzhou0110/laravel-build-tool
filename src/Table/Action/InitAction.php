<?php
namespace ZhouOu\LaravelTool\Table\Action;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhouOu\LaravelTool\Table\Model\Exception\ModelInitException;
use ZhouOu\LaravelTool\Tool\ConfigTool;
use ZhouOu\LaravelTool\Tool\ReturnInfoTool;

abstract class InitAction
{
    /**
     * @var static|Builder
     */
    protected $model;


    public function __construct()
    {
        try {
            $this->_init();
        } catch (Exception $e) {
            throw new ModelInitException($e->getMessage());
        }
    }

    private function _init()
    {
        $this->model = app()->make($this->model());

        if (!$this->model instanceof Model) {
            throw new Exception("Class {$this->model()} must be an instance of Illuminate\Database\Eloquent\Model");
        }
    }

    /**
     * @Function: model
     * @Notes: 初始化模型类名
     * @return mixed
     */
    abstract public function model() : string ;

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * @param array $where
     * @param array $data
     * @return int
     */
    public function update(array $where, array $data)
    {
        return $this->model->where($where)->update($data);
    }

    /**
     * @param array $where
     * @return mixed
     */
    public function destroy(array $where)
    {
        return $this->model->where($where)->delete();
    }


    /**
     * @Function: execute
     * @Notes: 执行sql
     * @Author: Joker
     * @Date: 2020-04-22  16:32
     *
     * @param $sql
     *
     * @return array
     * 
     */
    public function execute($sql)
    {
        try {
            $data =  \DB::update($sql);
        } catch (\Exception $e) {
            return ReturnInfoTool::msg(0, $e->getMessage());
        }
        return ReturnInfoTool::msg(1, '', ['ret' => $data > 0 ?? false]);
    }

    /**
     * Function: batchInsert
     * Notes: 批量插入。二维数组
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2020-04-19  22:14
     * @param array $data
     * @param string|null $table
     * @return array
     */
    public function batchInsert(array $data, string $table = null)
    {
         // return $this->model->insert($data);//有bug
        // 重写db的insert，原insert在5.8有问题
        if (!is_array(reset($data))) {
            $data = [$data];
        }
        // 获取key
        $keys = array_keys(reset($data));
        $keyStr = '(`' . implode("`,`", $keys) . '`)';
        unset($keys);
        // 获取表
        $table = $table ?? $this->model->table;
        // 封装sql
        $sql = "INSERT INTO {$table} {$keyStr} VALUES";
        foreach ($data as $item) {
            $sql .= " ('" . implode("','", $item) . "'),";
        }
        // 去除尾逗号
        $sql = rtrim($sql, ',');
        // 执行sql
        try {
            if (!\DB::insert(\DB::raw($sql))) {
                throw new \Exception('新增失败');
            }
            return ReturnInfoTool::msg(1, '新增成功');
        } catch (\Exception $e) {
            return ReturnInfoTool::msg(0, $e->getMessage());
        }
    }


    /**
     * Function: batchUpdate
     * Notes: 批量更新
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2020-04-06  15:41
     * @param array $updateData
     * @param string $queryColumn
     * @param string|null $tableName
     * @return array|void
     */
    public function batchUpdate(array $updateData, string $queryColumn = 'id', string $tableName = null)
    {
        try {
            if (count($updateData) == 0) {
                throw new \Exception('数据为空，无法执行更新');
            }
            // 需要更新的字段数据
            $needUpdateColumn = array_diff(array_keys($updateData[0]), [$queryColumn]);
            // 获取表名
            $tableName = $tableName ?? $this->model->getTable();
            // 解决一次性数据过多，导致速度反而过慢
            $num = count($updateData);
            $divideNum = ConfigTool::get('table_batch_update_divide_num') ?? 200;
            if ($num > $divideNum) {
                $num = ceil($num / $divideNum); // 需要分多少次
                $data = [];
                $start = 0;
                while ($start < $num) {
                    $data[] = array_slice($updateData, $start * $divideNum, $divideNum);
                    $start++;
                }
            } else {
                $data = [$updateData];
            }
            \DB::beginTransaction();
            // 拼接sql
            foreach ($data as $item) {
                $sql = "UPDATE $tableName SET";
                // 组装要更新的字段
                foreach ($needUpdateColumn as $column) {
                    $sql .= " $column = CASE";
                    foreach ($item as $value) {
                        $sql .= " WHEN $queryColumn = '{$value[$queryColumn]}'";
                        $sql .= " THEN '$value[$column]'";
                    }
                    $sql .= " ELSE $column END,"; // 不是就保持不变
                }
                // 去除为逗号
                $sql = rtrim($sql, ',');
                $sql .= " WHERE $queryColumn IN('" . implode("','", array_column($item, 'nns_id')) . "')";
                // echo $sql;die;
                if (!\DB::update(\DB::raw($sql))) {
                    throw new \Exception('更新失败');
                }
            }
            \DB::commit();
            return ReturnInfoTool::msg(1, '更新成功');
        } catch (\Exception $e) {
            \DB::rollback();
            return ReturnInfoTool::msg(0, $e->getMessage());
        }
    }
}
