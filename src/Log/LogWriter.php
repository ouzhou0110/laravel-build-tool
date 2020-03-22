<?php


namespace ZhouOu\LaravelTool\Log;


use ZhouOu\LaravelTool\Tool\ConfigTool;

class LogWriter
{
    // 日志单例
    private static $instance;

    // 文件最大值-单位M
    private $fileMaxSize = 100;

    // 每隔多少时间创建新的日志文件
    // 时间单位-d:天，h:小数，m: 分钟，最多一天，默认30分钟
    private $fileMaxTime = '30m';

    // 已打开但为关闭的文件句柄
    private $openedFileSource = [];


    // 日志文件前缀
    private $prefix = 'log_';

    // 日志文件后缀
    private $ext = 'txt';

    // 全部日志配置
    private $allConfig;

    // 配置信息
    private $config;

    private function __construct()
    {
        $this->allConfig = ConfigTool::get('zhouOuConfig');
    }

    /**
     * @Function: init
     * @Notes: 初始化日志
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  17:54
     *
     *
     * @return LogWriter
     *
     */
    public static function init()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * @Function: write
     * @Notes: 写入日志
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  17:55
     * @param string $tag 写入什么级别日志 debug_log | system_log
     * @param array $msg 日志数据
     */
    public function write(string $tag = 'debug_log', array $msg = ['error' => [], 'info' => []])
    {
        $this->config = $this->allConfig[$tag];
        foreach ($msg as $tag => $message) {
            if (count($message) <= 0) {
                continue;
            }
            // 创建或者获取日志文件路径文件
            $log_path = $this->_getLogPath($tag);
            // 获取文件句柄
            $fs = $this->_getFileSource($log_path);
            foreach ($message as $line) {
                // 执行写入
                fwrite($fs, $line);
            }
            // 写入分割符
            fwrite($fs, "\r\n\r\n");
        }

    }

    /**
     * @Function: _getLogPath
     * @Notes: 获取日志文件路径
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  20:11
     *
     * @param string $tag error | info
     *
     * @return string
     *
     */
    private function _getLogPath(string $tag)
    {
        // 获取当前时间，生成对应的日志文件
        $divideTime = strlen($this->config['log_divide_time']) > 0 ? $this->config['log_divide_time'] : $this->fileMaxTime;
        $fileName = $this->_getFileName($divideTime);
        $configPath = trim($this->config['log_path'], '/');
        $datePath = date('Y/m/d');
        return "../{$configPath}/{$tag}/{$datePath}/{$fileName}";
    }

    /**
     * @Function: _getFileSource
     * @Notes: 获取对应的文件句柄
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  20:13
     *
     * @param string $fileName
     *
     * @return bool|false|mixed|resource
     *
     */
    private function _getFileSource(string $fileName)
    {
        $fileName = app_path($fileName);
        // 检测目录是否存在
        $dir = dirname($fileName);
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0777, true)) {
                return false;
            }
        }

        // 检测文件是否达到限制，达到限制就切割日志文件
        if (file_exists($fileName)) {
            $fileSize = filesize($fileName) / (1024 * 1024);
            $fileMaxSize = strlen($this->config['log_file_max_size']) > 0 ? $this->config['log_file_max_size'] : $this->fileMaxSize;
            if ($fileSize >= $fileMaxSize) {
                $prefix = substr($fileName, 0, strrpos($fileName, '.'));
                $tag = substr($fileName, strpos($fileName,'T') + 1, 6);
                $subfix = substr($fileName, strrpos($fileName, '.'));
                $fileName = "{$prefix}_{$tag}$subfix";
                unset($prefix);
                unset($tag);
                unset($subfix);
            }
        }

        // 检测是否存在句柄
        $key = md5($fileName);
        if (isset($this->openedFileSource[$key]) && is_resource($this->openedFileSource[$key])) {
            return $this->openedFileSource[$key];
        }

        // 没有资源，创建文件资源句柄
        $fs = fopen($fileName, 'a+');
        if ($fs === false) {
            return false;
        }
        $this->openedFileSource[$key] = $fs;
        return $fs;
    }

    /**
     * @Function: _getFileName
     * @Notes: 获取对应的文件名
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  20:13
     *
     * @param string $limitTime 每隔多少时间切割一下日志
     *
     * @return string
     *
     */
    private function _getFileName(string $limitTime) : string
    {
        // 容错处理
        $time = intval(substr($limitTime, 0, -1));
        $unit = substr($limitTime, -1);
        if ($time == 0) {
            $time = 20;
            $unit = 'm';
        }
        // 文件后缀
        $ext = strlen($this->config['log_ext']) > 0 ? $this->config['log_ext'] : $this->ext;
        // 文件前缀
        $prefix = strlen($this->config['log_prefix']) > 0 ? $this->config['log_prefix'] : $this->prefix;
        switch ($unit) {
            case 'd':
                if ($time > 1) {
                    $time = 1;
                }
                $fileName = date('Ymd') . "T000000.$ext";
                break;
            case 'h':
                if ($time >= 24) {
                    $fileName = date('Ymd') . "T000000.$ext";
                } else {
                    $h  = date('H') -  ( date('H') % $time);
                    $h = $h < 10 ? "0{$h}" : $h;
                    $fileName = date('Ymd') . "T{$h}0000.$ext";
                }
                break;
            case 'm':
            default:
                if ($time > (24 * 60)) {
                    $fileName = date('Ymd') . "T000000.$ext";
                } else {
                    $m  = date('i') -  ( date('i') % $time);
                    $m = $m < 10 ? "0{$m}" : $m;
                    $fileName = date('Ymd') .'T'. date('H'). "{$m}00.$ext";
                }

        }
        return $prefix . $fileName;
    }



    /**
     * 关闭文件句柄
     */
    public function __destruct()
    {
        if (count($this->openedFileSource) < 0) {
            return true;
        }

        foreach ($this->openedFileSource as $fp) {
            fclose($fp);
        }
    }
}
