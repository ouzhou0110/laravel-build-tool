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
    public static function get($something = 'rand')
    {

        $result = dechex(  time() );
        $result = $result.dechex( np_millisecond() );

        $a = "";
        if( isset( $_ENV ["COMPUTERNAME"] ) )
            $a .= $_ENV ["COMPUTERNAME"];
        if( isset( $_SERVER ["SERVER_ADDR"] ) )
            $a .= $_SERVER ["SERVER_ADDR"];
        if( isset( $_SERVER ["REMOTE_ADDR"] ) )
            $a .= $_SERVER ["REMOTE_ADDR"];

        //echo $a;

        $a = $a.rand(0,10000);
        $a = $a.rand(0,10000);
        $a = $a.rand(0,10000);
        $a = $a.microtime ();


        $result = $result.md5( $a.$something );
        return substr( $result, 0, 32 );
    }
}

function np_millisecond()
{
    list ( $usec, $sec ) = explode ( ' ', microtime () );
    return intval( substr ( $usec, 2, 3 ) );
}
