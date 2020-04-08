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
                $data = $updateData;
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
                        $sql .= " THEN $value[$column]";
                    }
                    $sql .= " ELSE $column"; // 不是就保持不变
                }
                echo $sql;
            }
        } catch (\Exception $e) {

        }
    }
}
