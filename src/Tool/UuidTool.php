<?php


namespace ZhouOu\LaravelTool\Tool;


class UuidTool
{
    /**
     * @Function: get
     * @Notes: 随机数 + ip + 时间 + 随机数
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-22  10:27
     *
     *
     * @return string
     *
     */
    public static function get()
    {
        try
        {
            $rand1 = random_bytes(5);
            $time = date('ymdHis') . microtime();
            $ip = IpTool::get_client_ip_proxy_support();
            $rand2 = random_bytes(5);

        } catch (\Exception $e)
        {
            $rand1 = rand(10000, 99999);
            $ip = IpTool::get_client_ip_proxy_support();
            $time = date('ymdHis') . microtime(true);
            $rand2 = rand(10000, 99999);
        }

        return md5($rand1 . $time . $ip . $rand2);
    }
}
