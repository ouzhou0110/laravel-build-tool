<?php
namespace ZhouOu\LaravelTool\Log;

use ZhouOu\LaravelTool\Tool\ConfigTool;

class SystemLog
{
    // 日志单例
    private static $instance;

    // 日志信息储存
    private $messages = ['error' => [], 'info' => []];

    // 文件最大值-单位M
    private $fileMaxSize = 100;

    // 每隔多少时间创建新的日志文件
    // 时间单位-d:天，h:小数，m: 分钟，最多一天，默认30分钟
    private $fileMaxTime = '30m';

    // 已打开但为关闭的文件句柄
    private $openedFileSource = [];

    // 是否直接实时写入文件
    private $limitWriteEnable = false;

    // 仿进程id，区分一次请求的日志
    private $flag;

    // 日志文件前缀
    private $prefix = 'log_';

    // 日志文件后缀
    private $ext = 'txt';

    // 配置信息
    private $config;

    // 错误等级对照表
    private const ERROR_INFO = [
        E_ERROR => 'error',
        E_WARNING => 'warning',
        E_NOTICE => 'notice',
    ];

    private function __construct()
    {
        $this->flag = rand(10000,99999);
        $this->config = ConfigTool::get('zhouOuConfig')['systemLog'];
    }

    /**
     * @Function: init
     * @Notes: 初始化日志
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  17:54
     *
     *
     * @return SystemLog
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
     * @Function: errorHandler
     * @Notes: 错误处理
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  17:54
     *
     * @param int $severity 错误级别
     * @param string $msg 错误信息
     * @param string $file 发生错误的文件路径
     * @param string $line 第几行
     *
     * @return bool
     */
    public function errorHandler(int $severity, string $msg, string $file, string $line)
    {
        $microtime = (string)microtime(true);
        $microtime = substr($microtime, strpos($microtime, '.') + 1, 4);
        $time = date('Y-m-d H:i:s.') . $microtime;
        $errorLevel = isset(self::ERROR_INFO[$severity]) ? self::ERROR_INFO[$severity] : 'warning';
        $msg = $this->flag . "[$time][$errorLevel]$msg($file [line:$line ]) \r\n";
        $backtrace = debug_backtrace();
        unset($backtrace[0]);
        foreach ($backtrace as $item) {
            !isset($item['class']) && $item['class'] = 'NoClass';
            !isset($item['file']) && $item['file'] = 'NoFile';
            !isset($item['line']) && $item['line'] = 'NoLine';
            !isset($item['function']) && $item['function'] = 'NoFunction';
            $msg .= $this->flag . "[$time]{$item['file']}({$item['line']}): {$item['class']}::{$item['function']} \r\n";
        }

        if ($severity == E_ERROR) {
           $this->messages['error'][] = $msg;
        } else {
            $this->messages['info'][] = $msg;
        }
        $limitWrite = strlen($this->config['logLimitWriteEnable']) > 0 ?  $this->config['logLimitWriteEnable'] : $this->limitWriteEnable;
        if($limitWrite == true) {
            $this->write();
        }
        return true;
    }

    /**
     * @Function: exceptionHandler
     * @Notes: 异常处理
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  17:55
     *
     * @param \Exception $e
     *
     * @return bool
     *
     */
    public function exceptionHandler(\Exception $e)
    {
        $fieldMap = [
            'severity' => 0,
            'message' => 1,
            'file' => 2,
            'line' => 3
        ];
       $msg = '';
       $severity = 8;
       $microtime = (string)microtime(true);
       $microtime = substr($microtime, strpos($microtime, '.') + 1, 4);
       $time = date('Y-m-d H:i:s.') . $microtime;
       $backtrace = $e->getTrace();
       foreach ($backtrace as $key => $item) {
           if ($key == 0) {
               $item = $item['args'];
               $severity = $item[$fieldMap['severity']];
               $errorLevel = isset(self::ERROR_INFO[$severity]) ? self::ERROR_INFO[$severity] : 'warning';
               $msg .= $this->flag . "[$time][$errorLevel] msg: {$item[$fieldMap['message']]} (file: {$item[$fieldMap['file']]} [line:{$item[$fieldMap['line']]} ]) \r\n";
           } else {
               $msg .= $this->flag . "[$time]{$item['file']}({$item['line']}): {$item['class']}::{$item['function']} \r\n";
           }
       }

        if ($severity == E_ERROR) {
            $this->messages['error'][] = $msg;
        } else {
            $this->messages['info'][] = $msg;
        }

        $limitWrite = strlen($this->config['logLimitWriteEnable']) > 0 ?  $this->config['logLimitWriteEnable'] : $this->limitWriteEnable;
        if($limitWrite == true) {
            $this->write();
        }
        return true;
    }

    /**
     * @Function: write
     * @Notes: 写入日志
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-19  17:55
     *
     */
    public function write()
    {
        foreach ($this->messages as $tag => $message) {
            if (count($message) <= 0) {
                continue;
            }
            // 创建或者获取日志文件路径文件
            $logPath = $this->_getLogPath($tag);
            // 获取文件句柄
            $fs = $this->_getFileSource($logPath);
            foreach ($message as $line) {
                // 执行写入
                fwrite($fs, $line);
            }
            // 写入分割符
            fwrite($fs, "\r\n\r\n");
            // 清除日志
            $this->_clear($tag);
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
        // $path = './storage/logs/zhouou/logs/info/2020/03/19/20200319050500.txt';
        // 获取当前时间，生成对应的日志文件
        $divideTime = strlen($this->config['logDivideTime']) > 0 ? $this->config['logDivideTime'] : $this->fileMaxTime;
        $fileName = $this->_getFileName($divideTime);
        $configPath = trim($this->config['logPath'], '/');
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
            $fileMaxSize = strlen($this->config['logFileMaxSize']) > 0 ? $this->config['logFileMaxSize'] : $this->fileMaxSize;
            if ($fileSize >= $fileMaxSize || true) {
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
        $ext = strlen($this->config['logExt']) > 0 ? $this->config['logExt'] : $this->ext;
        // 文件前缀
        $prefix = strlen($this->config['logPrefix']) > 0 ? $this->config['logPrefix'] : $this->prefix;
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
     * @Function: _clear
     * @Notes: 定义的错误等级：error和info
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-18  22:49
     *
     * @param string $tag error | info
     */
    private function _clear(string $tag)
    {
        $this->messages[$tag] = [];
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

