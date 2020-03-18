<?php
namespace ZhouOu\LaravelTool\Traits;

use ZhouOu\LaravelTool\Tool\ApiResponse;

trait ApiResponseInjector
{
    private static $singleton;

    /**
     * @return ApiResponse
     */
    public function api()
    {
        if (!self::$singleton instanceof ApiResponse) {
            self::$singleton = new ApiResponse();
        }
        return self::$singleton;
    }
}
