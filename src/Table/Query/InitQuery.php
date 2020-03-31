<?php
namespace ZhouOu\LaravelTool\Table\Query;

use Illuminate\Database\Eloquent\Builder;
use ZhouOu\LaravelTool\Table\Model\Exception\ModelInitException;
use ZhouOu\LaravelTool\Table\Model\Exception\ModelNotFoundException;
use ZhouOu\LaravelTool\Table\Model\InitModel;

abstract class InitQuery
{
    /**
     * 使用web请求模式格式化数据
     */
    private const WEB_TAG = 'web';

    /**
     * 使用api模式格式化数据
     */
    private const API_TAG = 'api';
    /**
     * @var static|Builder
     */
    protected $model;

    /**
     * CommonRepository constructor.
     * @throws ModelInitException
     */
    public function __construct()
    {
        try {
            $this->_init();
        } catch (\Exception $e) {
            throw new ModelInitException();
        }
    }

    /**
     * @throws \Exception
     */
    private function _init()
    {
        $model = app()->make(static::model());
        if (!$model instanceof InitModel) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\Database\Eloquent\Model");
        }
        $this->model = $model;
    }

    /**
     * @return string 可以实例化的模型类名
     */
    abstract protected function model();

    /**
     * @param       $id
     * @param array $column
     * @return InitModel
     * @throws ModelNotFoundException
     */
    public function find($id, array $column = ['*'])
    {
        $result = $this->model->find($id, $column);
        if (is_null($result)) {
            throw new ModelNotFoundException();
        }
        return $result;
    }

    /**
     * @Function: getList
     * @Notes: 获取分页数据
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-18  11:45
     *
     * @param int $pageSize
     * @param int $page
     * @param array $where
     * @param array $column
     * @param string $tag web or api，
     * tag为web时，使用laravel自带paginate
     * tag为api时，使用apiPaginate,数据跟符合api标准
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|array
     *
     */
    public function getList(int $pageSize = 10, int $page = 1,array $where = [], array $column = ['*'], string $tag = 'web')
    {
        if ($tag === self::WEB_TAG) {
            $data = $this->model->where($where)->paginate($pageSize, $column,'page', $page);
        } else {
            $data = $this->model->where($where)->select($column)->apiPaginate($pageSize, $page);
        }
        return $data;
    }

    /**
     * @Function: getCount
     * @Notes: 返回数据总数
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-18  11:50
     *
     * @param array $where
     * @param string $column
     *
     * @return int
     *
     */
    public function getCount(array $where, string $column = '1')
    {
        return $this->model->where($where)->count($column);
    }

    /**
     * @Function: first
     * @Notes: 获取第一条数据
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-18  14:01
     *
     * @param array $where
     * @param array $column
     *
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     *
     */
    public function first(array $where, array $column = ['*'])
    {
        return $this->model->where($where)->select($column)->first();
    }

    /**
     * @Function: get
     * @Notes:
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-18  14:25
     *
     * @param array $where
     * @param array $columns
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     *
     */
    public function get(array $where,array $columns = ['*'])
    {
        return $this->model->where($where)->select($columns)->latest()->get();
    }

    /**
     * @Function: getPluck
     * @Notes: 获取单个字段数据
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-18  15:01
     *
     * @param string $column
     * @param array $where
     *
     * @return array
     *
     */
    public function getPluck(string $column, array $where = [])
    {
        return $this->model->where($where)->pluck($column)->toArray();
    }

}
