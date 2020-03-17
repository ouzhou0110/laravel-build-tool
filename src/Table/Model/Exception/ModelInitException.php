<?php
namespace ZhouOu\LaravelTool\Table\Model\Exception;

use Exception;
use Throwable;

class ModelInitException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        empty($message) && $message = '初始化模型失败: ' . $message;
        parent::__construct($message, $code, $previous);
    }
}
