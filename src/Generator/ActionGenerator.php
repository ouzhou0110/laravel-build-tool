<?php
namespace ZhouOu\LaravelTool\Generator;

class  ActionGenerator extends BaseGenerator
{

    /**
     * @inheritDoc
     */
    public static function init($baseConfig, $sonConfig)
    {
        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        // 组装model基本信息
        $baseInfo = [
            'fatherPath' => $commonPath . '/Actions/Commons/CommonModel.txt',
            'selfPath' => $commonPath . '/Actions' . $sonConfig['filePath'] . '/' . $sonConfig['fileName'] . 'Action.php',
            'templatePath' => '../Table/Action/Template/ChildTemplateAction.txt',
            'replace' => [
                '@{fatherNamespace}' => $baseConfig['tableNamespace'] . '\Actions\Commons\CommonAction',
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Actions' . $sonConfig['namespace'],
                '@{selfModelNamespace}' => $baseConfig['tableNamespace'] . '\Models' .  $sonConfig['namespace'],
                '@{selfClassName}' => $sonConfig['fileName'] . 'Action',
                '@{selfModelName}' => $sonConfig['fileName'] . 'Model',
            ],
        ];

        // 类型
        $baseInfo['actionType'] = 'action';

        self::build($baseInfo);
    }
}
