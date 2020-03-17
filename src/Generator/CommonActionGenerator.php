<?php
namespace ZhouOu\LaravelTool\Generator;

use ZhouOu\LaravelTool\Tool\ConfigTool;

/**
 * Class CommonActionGenerator
 * @Notes: 生成通用的Action类
 * @Author: zhou.ou
 * @Email: <zhou.ou@starcor.com>
 * @Date: 2020-03-12  16:31
 *
 * @package ZhouOu\LaravelTool\Generator
 */
class CommonActionGenerator extends BaseGenerator
{

    public static function init($baseConfig, $sonConfig)
    {
        $baseConfig = ConfigTool::get('tableConfig');

        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        $baseInfo = [
            'selfPath' => $commonPath . '/Actions/Commons/CommonAction.php',
            'templatePath' => '../Table/Action/Template/CommonAction.txt',
            'replace' => [
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Actions\Commons',
            ],
        ];

        self::build($baseInfo);
    }
}
