<?php
namespace ZhouOu\LaravelTool\Table\Action;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use ZhouOu\LaravelTool\Table\Model\Exception\ModelInitException;

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
        // 筛选数据
        $needUpdateColumn = array_diff(array_keys($updateData[0]), [$tableName]);
        // 获取表名

        // 获取需要更新的字段
        // 解决一次性数据过多，导致速度反而过慢
        $num = count($updateData);

        // 拼接sql
    }
}
