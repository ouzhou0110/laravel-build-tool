<?php
namespace ZhouOu\LaravelTool\Tool;
use phpDocumentor\Reflection\Types\Mixed_;
use ZhouOu\LaravelTool\Log\SystemLog;

final class ConfigTool
{
    private static $config = null;
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
        if ( null == self::$config) {
            if (!$tableBaseConfig = config($name)) {
                $tableBaseConfig = require_once __DIR__ . "/../Config/{$name}.php";
                if (true !== $tableBaseConfig) {
                    self::$config = $tableBaseConfig;
                }
            } else {
                self::$config = $tableBaseConfig;
            }
        }
        return self::$config;
    }

    /**
     * @Function: check
     * @Notes:
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  10:32
     *
     * @param $names
     *
     * @return array
     * (
     *      ret => 1失败，0成功
     *      reason => 错误原因
     * ）
     *
     */
    public static function check($names)
    {
        if (is_string($names)) {
            $names = explode(',', $names);
        }
        if (!is_array($names)) {
            return ReturnInfoTool::msg(0, '数据无法解析为数组，请核实');
        }
        foreach ($names as $name) {
            $config = static::$config;
            $tmp = explode('.', $name);
            $tmpName = '';
            foreach ($tmp as $item) {
                $tmpName .= ".$item";
                if (!isset($config[$item]) || (is_string($config[$item]) && strlen($config[$item]) == 0)) {
                    $tmpName = ltrim('.', $tmpName);
                    $msg = "配置文件zhouOuConfig.php中参数$tmpName 不存在或为空，请检测";
                    SystemLog::init()->error($msg);
                    return ReturnInfoTool::msg(0, $msg);
                }
                // 配置设置了，继续递归
                $config = $config[$item];
            }
        }

        return ReturnInfoTool::msg(1, '配置正确');
    }
}
