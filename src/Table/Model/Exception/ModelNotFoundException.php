<?php

/**
*
* Author: Joker-oz
* Date: 2019-09-09
*
*/
namespace ZhouOu\LaravelTool\Table\Model\Exception;

use Exception;
use Throwable;

class ModelNotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        empty($message) && $message = '模型不存在: ' . $message;
        parent::__construct($message, $code, $previous);
    }
}
