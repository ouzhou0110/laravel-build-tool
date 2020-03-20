<?php


namespace ZhouOu\LaravelTool\Log;


use ZhouOu\LaravelTool\Tool\ConfigTool;

class DebugLog extends Log
{

    // 仿进程id，区分一次请求的日志
    protected $flag;

    private static $instance;

    private $config;

    private function __construct()
    {
        $this->flag = rand(10000,99999);
        $this->config = ConfigTool::get('zhouOuConfig')['debugLog'];
    }

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
            LogWriter::init()->write('debugLog', $this->messages);
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
            LogWriter::init()->write('debugLog', $this->messages);
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function write()
    {
       LogWriter::init()->write('debugLog', $this->messages);
    }
}
