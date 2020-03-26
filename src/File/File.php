<?php


namespace ZhouOu\LaravelTool\File;


use ZhouOu\LaravelTool\Tool\ConfigTool;

class File
{
    /**
     * @Function: getFiles
     * @Notes:
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-26  10:27
     *
     * @param string $tag
     *
     * @return array [
     *  [
     *      fileName: 真实名称,
     *      fileTmpName: 临时文件路径,
     *      fileSize: 文件大小
     *  ],
     * ]
     */
    public  function getFiles(string $tag = 'file')
    {
        return $this->getByFormData($tag);

    }

    /**
     * @Function: getByFormData
     * @Notes: 处理上传的文件
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-26  13:57
     *
     * @param $tag
     *
     * @return array
     *
     */
    private  function getByFormData($tag)
    {
        $files = $_FILES[$tag];
        if (count($files['name']) == 0) {
            return [];
        }
        // 处理数据
        $data = [];
        foreach ($files['name'] as $key => $value) {
            $data[] = [
                'fileName' => $files['name'][$key],
                'fileTmpName' => $files['tmp_name'][$key],
                'fileSize' => $files['size'][$key],
            ];
        }
        return $data;
    }

    public function getExtension($name)
    {
        return substr($name, strrpos($name, '.') + 1);
    }

    /**
     * @Function: formatFileUrl
     * @Notes: 返回对应的资源路径
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-26  15:03
     *
     * @param $url
     *
     * @return string
     *
     */
    public static function formatFileUrl($url)
    {
        $arr = explode(':', $url);
        if (count($arr) == 2) {
            if ($arr[0] == 'local') {
                return url( $arr[1]);
            } else if ($arr[0] == 'ftp') {
                $ftpConfig = ConfigTool::get('zhouOuConfig')['ftp'];
                return 'ftp://' . $ftpConfig['ftp_host'] . ':' . $ftpConfig['ftp_port'] . $arr[1];
            }
        } else {
            return $url;
        }
    }
}
