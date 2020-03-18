<?php
namespace ZhouOu\LaravelTool\Generator;

class  QueryGenerator extends BaseGenerator
{

    /**
     * @inheritDoc
     */
    public static function init($baseConfig, $sonConfig)
    {
        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        // 组装model基本信息
        $baseInfo = [
            'fatherPath' => $commonPath . '/Queries/Commons/CommonModel.txt',
            'selfPath' => $commonPath . '/Queries' . $sonConfig['filePath'] . '/' . $sonConfig['fileName'] . 'Query.php',
            'templatePath' => '../Table/Query/Template/ChildTemplateQuery.txt',
            'replace' => [
                '@{fatherNamespace}' => $baseConfig['tableNamespace'] . '\Queries\Commons\CommonQuery',
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Queries' . $sonConfig['namespace'],
                '@{selfModelNamespace}' => $baseConfig['tableNamespace'] . '\Models' .  $sonConfig['namespace'],
                '@{selfClassName}' => $sonConfig['fileName'] . 'Query',
                '@{selfModelName}' => $sonConfig['fileName'] . 'Model',
            ],
        ];

        // 类型
        $baseInfo['actionType'] = 'query';

//        var_export($baseInfo);die;
        self::build($baseInfo);
    }
}
