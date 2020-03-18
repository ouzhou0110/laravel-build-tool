<?php
namespace ZhouOu\LaravelTool\Generator;

use ZhouOu\LaravelTool\Tool\ConfigTool;

/**
 * Class CommonQueryGenerator
 * @Notes: 生成通用的Query类
 * @Author: zhou.ou
 * @Email: <zhou.ou@starcor.com>
 * @Date: 2020-03-12  16:31
 *
 * @package ZhouOu\LaravelTool\Generator
 */
class CommonQueryGenerator extends BaseGenerator
{

    public static function init($baseConfig, $sonConfig)
    {
        $baseConfig = ConfigTool::get('tableConfig');

        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        $baseInfo = [
            'selfPath' => $commonPath . '/Queries/Commons/CommonQuery.php',
            'templatePath' => '../Table/Query/Template/CommonQuery.txt',
            'replace' => [
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Queries\Commons',
            ],
        ];

        self::build($baseInfo);
    }
}
