<?php
namespace ZhouOu\LaravelTool\Generator;

use ZhouOu\LaravelTool\Tool\ConfigTool;

/**
 * Class CommonLogicGenerator
 * @Notes: 生成通用的Logic类
 * @Author: zhou.ou
 * @Email: <zhou.ou@starcor.com>
 * @Date: 2020-03-12  16:31
 *
 * @package ZhouOu\LaravelTool\Generator
 */
class CommonLogicGenerator extends BaseGenerator
{

    public static function init($baseConfig, $sonConfig)
    {
        $baseConfig = ConfigTool::get('zhouOuConfig');

        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        $baseInfo = [
            'selfPath' => $commonPath . '/Logics/Commons/CommonLogic.php',
            'templatePath' => '../Table/Logic/Template/CommonLogic.txt',
            'replace' => [
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Logics\Commons',
            ],
        ];

        self::build($baseInfo);
    }
}
