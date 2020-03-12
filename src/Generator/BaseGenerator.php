<?php
namespace ZhouOu\LaravelTool\Generator;

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
        if ($have_father == true && !file_exists($config['fatherPath'])) {
            if (false === CommonModelGenerator::init([],[])) {
                self::msg('[warning]父类不存在，请手动创建：' . $config['fatherPath']);
            }
        }
        // 检测自身是否存在
        if (file_exists($config['selfPath'])) {
            // 排除子级创建时，一直报父级存在错误
            if ($have_father == true) {
                self::msg('[fatal error]当前model已经存在，无法重复创建:' . $config['selfPath']);
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
        foreach ($config['replace'] as $search => $replace) {
            $searchArr[] = $search;
            $replaceArr[] = $replace;
        }
        $template = str_replace($searchArr, $replaceArr, $template);
        // 生成文件
        self::save($config['selfPath'], $template);
    }

    /**
     * @Function: save
     * @Notes: 保存
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-12  16:33
     *
     * @param $path
     * @param $content
     *
     *
     */
    private static function save($path, $content)
    {
        // 判断上级目录是否存在
        $dir = dirname($path);
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                self::msg('[fatal error] 无法创建文件夹, model 生成失败：' . $dir);
                return;
            }
        }

        // 覆盖模式
        if (file_put_contents($path, $content, 0)) {
            self::msg('[success] model生成成功：' . $path);
        } else {
            self::msg('[fail] model生成失败' . $path);
        }
    }

    public static function msg($msg)
    {
        echo '[' . date('Y-m-d H:i:s') . "] $msg  \r\n";
    }
}
