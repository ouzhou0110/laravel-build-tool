<?php
namespace ZhouOu\LaravelTool\Tool;

class FileTool
{
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
     * @return bool
     */
    public static function save($path, $content)
    {
        // 判断上级目录是否存在
        $dir = dirname($path);
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                self::msg('[fatal error] 无法创建文件夹, 模板生成失败：' . $dir);
                return false;
            }
        }

        // 覆盖模式
        if (file_put_contents($path, $content, 0)) {
            self::msg('[success] 模板生成成功：' . $path);
            return true;
        }

        self::msg('[fail] 模板生成失败' . $path);
        return false;
    }

    public static function msg($msg)
    {
        $isPrint = ConfigTool::get('zhouOuConfig')['isOpenPrintDebugInfo'];
        if ($isPrint == false) {
            return;
        }
        echo '[' . date('Y-m-d H:i:s') . "] $msg  \r\n";
    }
}
