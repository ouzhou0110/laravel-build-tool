<?php

namespace ZhouOu\LaravelTool\Generator;

class ModelGenerator extends BaseGenerator
{
    public static function init($baseConfig, $sonConfig)
    {
        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        // 组装model基本信息
        $baseInfo = [
            'fatherPath' => $commonPath . '/Models/Commons/CommonModel.txt',
            'selfPath' => $commonPath . '/Models' . $sonConfig['filePath'] . '/' . $sonConfig['fileName'] . 'Model.php',
            'templatePath' => '../Table/Model/Template/ChildTemplateModel.txt',
            'replace' => [
                '@{fatherNamespace}' => $baseConfig['tableNamespace'] . '\Models\Commons\CommonModel',
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Models' .  $sonConfig['namespace'],
                '@{selfClassName}' => $sonConfig['fileName'] . 'Model',
                '@{selfTableName}' => $baseConfig['tablePrefix'] . $sonConfig['tableName'],
            ],
        ];
        $baseInfo['actionType'] = 'model';
        self::build($baseInfo);
    }
}
