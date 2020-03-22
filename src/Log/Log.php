<?php


namespace ZhouOu\LaravelTool\Log;


abstract class Log
{

    // 日志信息储存
    protected $messages = ['error' => [], 'info' => []];

    // 是否直接实时写入文件
    protected $limitWriteEnable = false;

    // 错误等级对照表
    protected const ERROR_INFO = [
        E_ERROR => 'error',
        E_WARNING => 'warning',
        E_NOTICE => 'notice',
    ];

    private function __construct(){}

    abstract public static function init();

    /**
     * @Function: _clear
     * @Notes: 定义的错误等级：error和info
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-18  22:49
     *
     * @param string $tag error | info
     */
    public function _clear(string $tag)
    {
        $this->messages[$tag] = [];
    }

    /**
     * @Function: write
     * @Notes: 写入日志文件
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-20  17:25
     *
     * @return mixed
     *
     */
    abstract public function write();

    /**
     * 回收数据
     */
    public function __destruct()
    {
        $this->_clear('error');
        $this->_clear('info');
    }
}
