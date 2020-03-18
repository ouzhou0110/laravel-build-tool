<?php
namespace ZhouOu\LaravelTool\Generator;

use ZhouOu\LaravelTool\Tool\FileTool;

/**
 * Class BaseGenerator
 * @Notes: model、action、logic、query底层模板生成器
 * @Author: zhou.ou
 * @Email: <zhou.ou@starcor.com>
 * @Date: 2020-03-12  16:31
 *
 * @package ZhouOu\LaravelTool\Generator
 */
abstract class BaseGenerator
{

    /**
     * @Function: init
     * @Notes: 属性初始化
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-12  16:32
     *
     * @param $baseConfig
     * @param $sonConfig
     *
     * @return mixed
     *
     */
    abstract public static function init($baseConfig, $sonConfig);

    private static $actionMap = [
        'action' => 'CommonActionGenerator',
        'model' => 'CommonModelGenerator',
        'query' => 'CommonQueryGenerator'
    ];

    /**
     * @Function: build
     * @Notes: 生成对应模型
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-12  16:32
     *
     * @param $config
     *
     *
     */
    protected static function build($config)
    {
        // 检测父级是否存在
        $have_father = (isset($config['fatherPath']) && strlen($config['fatherPath']) > 0);
        $actionType = '模板';
        if ($have_father == true && !file_exists($config['fatherPath'])) {
            $actionType = $config['actionType'];
            switch ($actionType) {
                case 'action':
                    $result = CommonActionGenerator::init([],[]);
                    break;
                case 'model':
                    $result = CommonModelGenerator::init([],[]);
                    break;
                case 'query':
                    $result = CommonQueryGenerator::init([],[]);
                    break;
                default:
                    $result = true;
            }
            if (false === $result) {
                self::msg('[warning]父类不存在，请手动创建：' . $config['fatherPath']);
            }
        }
        // 检测自身是否存在
        if (file_exists($config['selfPath'])) {
            // 排除子级创建时，一直报父级存在错误
            if ($have_father == true) {
                self::msg("[fatal error]当前$actionType 已经存在，无法重复创建:" . $config['selfPath']);
            }
            return;
        }
        // 判断模板是否存在
        if (!file_exists(__DIR__ . '/' . $config['templatePath'])) {
            self::msg('[fatal error] 包数据丢失，请重新安装！');
            return;
        }
        // 获取模板
        $template = file_get_contents(__DIR__ . '/' . $config['templatePath']);
        // 替换标识
        $searchArr = [];
        $replaceArr = [];
        unset($config['actionType']); // 排除干扰项
        foreach ($config['replace'] as $search => $replace) {
            $searchArr[] = $search;
            $replaceArr[] = $replace;
        }
        $template = str_replace($searchArr, $replaceArr, $template);
        // 生成文件
        FileTool::save($config['selfPath'], $template);
    }

    public static function msg($msg)
    {
        echo '[' . date('Y-m-d H:i:s') . "] $msg  \r\n";
    }

}
