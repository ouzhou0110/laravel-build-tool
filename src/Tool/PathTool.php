<?php
namespace ZhouOu\LaravelTool\Tool;

class PathTool
{
    /**
     * 用于搜索的标识
     */
    private const SEARCH_TAG = '\\';

    /**
     * 用于替换的标识
     */
    private const REPLACE_TAG = '/';


    /**
     * @Function: tablePathParse
     * @Notes: 处理table组件生成器的路径
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-11  15:22
     *
     * @param $path
     * @return array
     */
    public  function tablePathParse($path)
    {
        // 同一路径格式
        $path = $this->_formatPath($path);
        // 解析格式 xxx\tableName
        $pathArr = explode(self::REPLACE_TAG, $path);

        $lastKey = array_key_last($pathArr);

        // 驼峰式命名-保证它为大驼峰
        $tableName = ucfirst($pathArr[$lastKey]);
        // 转化标准命名
        $tableStandName = strtolower(substr(preg_replace('/([A-Z])/', '_\\1', $tableName), 1));

        // 处理路径
        unset($pathArr[$lastKey]);
        $pathInfo = implode(self::REPLACE_TAG, $pathArr);
        if (strlen($pathInfo) > 0) {
            $pathInfo = self::REPLACE_TAG . $pathInfo;
        }

        // 处理命名空间
        $namespace = '';
        foreach ($pathArr as $item) {
            $namespace .= '\\' . ucfirst($item);
        }

        return [
            'tableName' => $tableStandName,
            'fileName' => $tableName,
            'namespace' => $namespace,
            'filePath' => $pathInfo,
        ];
    }

    /**
     * @Function: _formatPath
     * @Notes: 同一地址格式为linux路径格式 'xx/xx/xx'
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-11  15:18
     *
     * @param $path
     *
     * @return string|string[]
     *
     */
    private function _formatPath($path)
    {
        return str_replace(self::SEARCH_TAG, self::REPLACE_TAG, $path);
    }
}
