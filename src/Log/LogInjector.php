<?php


namespace ZhouOu\LaravelTool\Log;


class LogInjector
{
    public function run()
    {
        // 注入错误处理机制
        set_error_handler([LogInjector::class, 'errorHandler']);

        // 注入异常处理机制
        set_exception_handler([LogInjector::class, 'exceptionHandler']);

        // 注入shutdown处理方法
        register_shutdown_function([LogInjector::class, 'shutdownHandler']);
    }

    public static function shutdownHandler()
    {
        DebugLog::init()->write();
        SystemLog::init()->write();
    }

    public function exceptionHandler($e)
    {
        DebugLog::init()->exceptionHandler($e);
    }

    public  function errorHandler($errNo, $errMsg, $errFile, $errLine)
    {
        DebugLog::init()->errorHandler($errNo, $errMsg, $errFile, $errLine);
    }
}
