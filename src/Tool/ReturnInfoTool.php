<?php


namespace ZhouOu\LaravelTool\Tool;


class ReturnInfoTool
{
    /**
     * @Function: msg
     * @Notes:
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  10:34
     *
     * @param int $code 1成功 0失败 2其他(自定义)
     * @param string $reason
     * @param array $data
     *
     * @return array
     *
     */
    public static function msg(int $code = 1, string $reason = '', array $data = [])
    {
        return [
            'code' => $code,
            'reason' => $reason,
            'data' => $data
        ];
    }
}
