<?php
namespace ZhouOu\LaravelTool\Table\Query\Extend;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class ApiPaginator
 * @Notes: 新增查询器方法-api分页
 * @Author: zhou.ou
 * @Email: <zhou.ou@starcor.com>
 * @Date: 2020-03-18  11:28
 *
 * @package ZhouOu\LaravelTool\Table\Query\Extend
 */
class ApiPaginator extends LengthAwarePaginator
{
    public static function inject()
    {
        Builder::macro('apiPaginate', function ($perPage, $page) {
            $perPage = $perPage ?: $this->model->getPerPage();
            $items = ($total = $this->toBase()->getCountForPagination())
                ? $this->forPage($page, $perPage)->get(['*'])
                : $this->model->newCollection();

            $data =  Container::getInstance()->makeWith(ApiPaginator::class, compact(
                'items', 'total', 'perPage', 'page'
            ));
            $list = $data->getCollection(); // 保持数据为collection
            $data = $data->toArray();
            return [
                'page_size' => $data['per_page'],
                'page' => $data['current_page'],
                'total' => $data['total'],
                'list' => $list,
            ];
        });
    }
}
