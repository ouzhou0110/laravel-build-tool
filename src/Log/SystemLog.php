<?php


namespace ZhouOu\LaravelTool\Log;

use Illuminate\Http\Request;
use ZhouOu\LaravelTool\Tool\ConfigTool;
use ZhouOu\LaravelTool\Tool\IpTool;

class SystemLog extends Log
{

    // 仿进程id，区分一次请求的日志
    protected  $flag;

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

    public function error(string $msg, $params = [])
    {
        $this->add('error', $msg . var_export($params, true));
    }

    /**
     * @Function: info
     * @Notes: 错误日志，写入system info
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-20  15:19
     *
     * @param string $msg
     * @param $params
     *
     */
    public  function info(string $msg, $params)
    {
        $this->add('info', $msg . var_export($params, true));
    }

    /**
     * @Function: request
     * @Notes: 请求日志
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-20  15:19
     *
     * @param Request $request
     *
     *
     */
    public  function request(Request $request)
    {
//        $data['account_id'] = JokerAuth::getUserId($request); // 操作者账号
        $data['http_code'] = http_response_code(); // 相应状态码
        $data['method'] = $request->method(); // 请求方法
        $data['host_protocol'] = $request->getSchemeAndHttpHost(); // 主机
        $data['uri'] = $request->getUri(); // uri
//        $data['action'] = Route::currentRouteAction(); // 请求路由
        $data['request_body'] = json_encode($request->all()); // 请求体--参数
        $data['content_type'] = $request->header('content-type'); // 内容类型
        $data['ip'] = IpTool::get_client_ip_proxy_support();; // 主机ip
        $data['referer'] = $request->server('HTTP_REFERER'); // 请求缘由--可以做防盗链，也可以伪造，所以不太可信
        $data['cookie'] = json_encode($request->cookies->all()); // cookie
        $data['token'] = $request->header('token'); // 令牌
        $data['user_agent'] = $request->userAgent(); // 用户代理
        /**
         * 拼接内容
         */
        $log = '';
        foreach ($data as $k => $item) {
            $log .= "\t". '{'. $k . ' => ' . ($item ?? '#') . '}';
        }
//
        $this->add('info', $log . var_export($request->all(), true));
    }


    /**
     * @Function: add
     * @Notes: 加入日志队列
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-20  17:45
     *
     * @param string $tag
     * @param string $msg
     *
     *
     */
    private  function add(string $tag, string $msg)
    {
        $microtime = (string)microtime(true);
        $microtime = substr($microtime, strpos($microtime, '.') + 1, 4);
        $time = date('Y-m-d H:i:s.') . $microtime;
        $msg = $this->flag . "[$time] $msg";
        $this->messages[$tag][] = $msg;
    }

    /**
     * @inheritDoc
     */
    public function write()
    {
        LogWriter::init()->write('systemLog', $this->messages);
    }

}
