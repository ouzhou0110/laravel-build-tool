<?php

namespace ZhouOu\LaravelTool\Log;

/**
 * Class SystemLogInjector
 * @Notes: 系统日志注入
 * @Author: zhou.ou
 * @Email: <zhou.ou@starcor.com>
 * @Date: 2020-03-19  22:39
 *
 * @package ZhouOu\LaravelTool\Log
 */
class SystemLogInjector
{
    public function run()
    {
        // 注入错误处理机制
        set_error_handler([SystemLogInjector::class, 'errorHandler']);

        // 注入异常处理机制
        set_exception_handler([SystemLogInjector::class, 'exceptionHandler']);

        // 注入shutdown处理方法
        register_shutdown_function([SystemLogInjector::class, 'shutdownHandler']);
    }

    public function shutdownHandler()
    {
        SystemLog::init()->write();
    }

    public function exceptionHandler($e)
    {
        SystemLog::init()->exceptionHandler($e);
    }

    public function errorHandler($errNo, $errMsg, $errFile, $errLine)
    {
        SystemLog::init()->errorHandler($errNo, $errMsg, $errFile, $errLine);
    }
}

//use ZhouOu\LaravelTool\Log\SystemLog;
//
//function shutdownHandler()
//{
//    var_export('run');
//    SystemLog::init()->write();
//}
//
//function exceptionHandler($e)
//{
//    var_export('s');
//    SystemLog::init()->exceptionHandler($e);
//}
//
//function errorHandler($errNo,$errMsg,$errFile,$errLine)
//{
//    var_export('b');
//    SystemLog::init()->errorHandler($errNo,$errMsg,$errFile,$errLine);
//}

// 注入错误处理机制
//set_error_handler( 'errorHandler');

// 注入异常处理机制
//set_exception_handler('exceptionHandler');
//
//// 注入shutdown处理方法
//register_shutdown_function('shutdownHandler');

//die;
