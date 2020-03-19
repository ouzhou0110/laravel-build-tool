<?php
namespace ZhouOu\LaravelTool\Tool;
final class ConfigTool
{
    private static $config;
    /**
     * @Function: get
     * @Notes: 读取配置
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-12  16:13
     *
     * @param $name
     *
     * @return \Illuminate\Config\Repository|mixed
     *
     */
    public static function get($name)
    {
        // 读取默认配置--框架中config目录下没有找到，就使用包中config
        if (!$tableBaseConfig = config($name)) {
            $tableBaseConfig = require_once __DIR__ . "/../Config/{$name}.php";
            if (true !== $tableBaseConfig) {
                self::$config = $tableBaseConfig;
            }
        }
        return self::$config;
    }
}
