<?php


namespace ZhouOu\LaravelTool\Generator;


class LogicGenerator extends BaseGenerator
{
    /**
     * @inheritDoc
     */
    public static function init($baseConfig, $sonConfig)
    {
        $commonPath = './' . trim(lcfirst(str_replace('\\', '/', $baseConfig['tableNamespace'])), '/');
        // 组装model基本信息
        $baseInfo = [
            'fatherPath' => $commonPath . '/Logics/Commons/CommonModel.txt',
            'selfPath' => $commonPath . '/Logics' . $sonConfig['filePath'] . '/' . $sonConfig['fileName'] . 'Logic.php',
            'templatePath' => '../Table/Logic/Template/ChildTemplateLogic.txt',
            'replace' => [
                '@{fatherNamespace}' => $baseConfig['tableNamespace'] . '\Logics\Commons\CommonLogic',
                '@{selfNamespace}' => $baseConfig['tableNamespace'] . '\Logics' . $sonConfig['namespace'],
                '@{selfClassName}' => $sonConfig['fileName'] . 'Logic',
            ],
        ];

        // 类型
        $baseInfo['actionType'] = 'logic';

        self::build($baseInfo);
    }
}
