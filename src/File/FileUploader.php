<?php


namespace ZhouOu\LaravelTool\File;


use Faker\Provider\Uuid;
use ZhouOu\LaravelTool\Log\SystemLog;
use ZhouOu\LaravelTool\Tool\ConfigTool;
use ZhouOu\LaravelTool\Tool\ReturnInfoTool;

class FileUploader extends File
{
    private $config = [];

    private $ftpConfig = [];

    private $driver;

    private $ftp = null;

    public function __construct()
    {
        $ret = ConfigTool::check(['file', 'file_upload_driver']);
        if ($ret['code'] == 1) {
            $this->config = ConfigTool::get('zhouOuConfig')['file'];
            $this->driver = ConfigTool::get('zhouOuConfig')['file_upload_driver'];
        } else {
            $this->driver = 'local';
        }

        if ($this->driver == 'ftp') {
            $this->ftpConfig = ConfigTool::get('zhouOuConfig')['ftp'];
            $this->ftp = new FTP();
            $this->ftp->connect();
        }

    }

    /**
     * @Function: save
     * @Notes:
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-22  9:44
     *
     * @param array $files 文件资源数组
     * @param array $exts
     * @param string $tag 存储位置标记
     * @param int|null $max 本次上传最大数量，默认使用配置
     *
     *
     * @return array
     */
    public function save(array $files,array $exts = [], string $tag = 'images', int $max = null)
    {
        // 检测最大数量显示
        $max = $max === null ? $this->config['file_max_num'] : $max;
        if ($max < count($files)) {
            $msg = "单次上传文件数量超过限制：$max  ";
            SystemLog::init()->info($msg, [$files, $tag, $max], 3);
            return ReturnInfoTool::msg(0, $msg);
        }
        // 获取允许的拓展
        if (count($exts) == 0) {
            $exts = $this->config['file_allow_ext'];
        }

        // 格式化路径
        $tag = $this->formatPath($tag);
        return $this->upload($files, $exts, $tag);
    }

    /**
     * Function: formatPath
     * Notes: 格式化路径
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2020-05-04  14:41
     * @param string $path
     * @return string
     */
    private function formatPath(string $path)
    {
        if (strlen($path) < 1) {
            return '';
        }

        return trim(str_replace('\\', '/', $path), '/');
    }


    /**
     * Function: upload
     * Notes: 上传文件
     * User: Joker
     * Email: <jw.oz@outlook.com>
     * Date: 2020-05-04  14:41
     * @param array $files
     * @param array $exts
     * @param string $tag
     * @return array
     * @throws \Exception
     */
    private function upload(array $files, array $exts, string $tag)
    {
        if ($this->driver == 'ftp') {
            return $this->uploadFtp($files, $exts, $tag);
        }
        return $this->uploadLocal($files, $exts, $tag);

    }

    /**
     * @Function: uploadLocal
     * @Notes: 上传到本地服务器
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-26  15:08
     *
     * @param $files
     * @param $exts
     *
     * @param string $tag
     * @return array
     */
    private function uploadLocal($files, $exts, string $tag)
    {
        // 返回指
        $data = [
            'failNum' => 0,
            'failList' => [],
            'successNum' => 0,
            'successList' => []
        ];

        // 创建文件路径
        $path = '/' . trim($this->config['file_save_path'], '/') . '/' . $tag . '/' . date('Y/m/d', time());
        if (!file_exists(public_path() . $path)) {
            mkdir(public_path() . $path, 0777, true);
        }

        // 循环写入文件
        foreach ($files as $file) {
            // 判断拓展名是否被允许
            $tmpExt = $this->getExtension($file['fileName']);
            if (!in_array($tmpExt, $exts)) {
                $data['failList'][] = $file['fileName'];
                $data['failNum']++;
                continue;
            }

            // 移动图片
            $fileName = Uuid::uuid() . ".$tmpExt";
            $res = move_uploaded_file($file['fileTmpName'], public_path($path) . '/' . $fileName);
            if (!$res) {
                $data['failList'][] = $file['fileName'];
                $data['failNum']++;
            } else {
                $data['successList'][] = "{$this->driver}:$path/$fileName";
                $data['successNum']++;
            }
        }

        return $data;
    }


    /**
     * @Function: uploadFtp
     * @Notes: 上传到ftp
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-26  15:09
     *
     * @param array $files
     * @param array $exts
     *
     * @param string $tag
     * @return array
     *
     * @throws \Exception
     */
    private function uploadFtp(array $files,array $exts, string $tag)
    {
        // 返回指
        $data = [
            'failNum' => 0,
            'failList' => [],
            'successNum' => 0,
            'successList' => []
        ];

        // 创建文件路径
        $path = '/' . trim($this->ftpConfig['ftp_file_path'], '/') . '/' . $tag . '/' . date('Y/m/d', time());

        // 循环写入文件
        foreach ($files as $file) {
            // 判断拓展名是否被允许
            $tmpExt = $this->getExtension($file['fileName']);
            if (!in_array($tmpExt, $exts)) {
                $data['failList'][] = $file['fileName'];
                $data['failNum']++;
                continue;
            }

            // 移动图片
            $fileName = Uuid::uuid() . ".$tmpExt";
            if ($this->ftp == null) {
                $this->ftp = new FTP();
                $this->ftp->connect();
            }
            $res = $this->ftp->put($path, $fileName,$file['fileTmpName']);
            if (!$res) {
                $data['failList'][] = $file['fileName'];
                $data['failNum']++;
            } else {
                $data['successList'][] = "{$this->driver}:$path/$fileName";
                $data['successNum']++;
            }
        }

        return $data;
    }

}
