<?php


namespace ZhouOu\LaravelTool\Tool;


class IpTool
{
    public static function get_client_ip_proxy_support()
    {

        if (isset($_SERVER))
        {

            if (isset($_SERVER['HTTP_CDN_SRC_IP']) && !empty($_SERVER['HTTP_CDN_SRC_IP']))
            {
                $ip = $_SERVER['HTTP_CDN_SRC_IP'];
                $ip = trim($ip);
                if (self::check_ip_valid($ip)) return $ip;
            }

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            {
                $ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
                $ips = urldecode($ips);
                $arr = explode(',', $ips);
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr as $ip)
                {
                    $ip = trim($ip);

                    if ($ip != 'unknown' && !empty($ip))
                    {
                        if (self::check_ip_valid($ip)) return $ip;
                    }
                }
            }


            if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
            {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
                $ip = trim($ip);
                if (self::check_ip_valid($ip)) return $ip;
            }

            if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
            {
                $ip = $_SERVER['REMOTE_ADDR'];
                $ip = trim($ip);
                if (self::check_ip_valid($ip)) return $ip;
            }
        }
        else
        {
            if (getenv('HTTP_X_FORWARDED_FOR'))
            {
                $ips = getenv('HTTP_X_FORWARDED_FOR');
                $ips = urldecode($ips);
                $arr = explode(',', $ips);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr as $ip)
                {
                    $ip = trim($ip);

                    if ($ip != 'unknown' && !empty($ip))
                    {
                        if (self::check_ip_valid($ip)) return $ip;
                    }
                }
            }
            if (getenv('HTTP_CLIENT_IP'))
            {
                $ip = getenv('HTTP_CLIENT_IP');
                $ip = trim($ip);
                if (self::check_ip_valid($ip)) return $ip;
            }
            if (getenv('REMOTE_ADDR'))
            {
                $ip = getenv('REMOTE_ADDR');
                $ip = trim($ip);
                if (self::check_ip_valid($ip)) return $ip;
            }
        }

        return '0.0.0.0';
    }

    private static function check_ip_valid($ip)
    {
        //如果是ipv6也返回true
        $bool = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
        if (!$bool)//ipv4 和ipv6都不是返回false
        {
            return FALSE;
        }
        else
        {
            return TRUE;
        }
    }
}
