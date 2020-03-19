<?php
namespace ZhouOu\LaravelTool\Generator;

use ZhouOu\LaravelTool\Tool\ConfigTool;

/**
 * Class CommonModelGenerator
 * @Notes: 生成通用的model类
 * @Author: zhou.ou
 * @Email: <zhou.ou@starcor.com>
 * @Date: 2020-03-12  16:31
 *
 * @package ZhouOu\LaravelTool\Generator
 */
class CommonModelGenerator extends BaseGenerator
{

    public static function init($baseConfig, $sonConfig)
    {
        $baseConfig = ConfigTool::get('zhouOuConfig');

        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        $baseInfo = [
            'selfPath' => $commonPath . '/Models/Commons/CommonModel.php',
            'templatePath' => '../Table/Model/Template/CommonModel.txt',
            'replace' => [
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Models\Commons',
            ],
        ];

        self::build($baseInfo);
    }
}
