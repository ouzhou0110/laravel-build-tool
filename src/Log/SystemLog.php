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
        $this->config = ConfigTool::get('zhouOuConfig')['debug_log'];
    }

    public static function init()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @Function: error
     * @Notes: 系统错误日志，建议少用
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  8:43
     *
     * @param string $msg
     * @param array $params
     * @param int $backtraceNum 记录调用栈数量
     */
    public function error(string $msg, $params = [], int $backtraceNum = 0)
    {

        $this->add('error', $msg , $params, $backtraceNum);
    }

    /**
     * @Function: info
     * @Notes: 错误日志，写入system info，建议使用
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-20  15:19
     *
     * @param string $msg
     * @param array $params
     * @param int $backtraceNum 记录调用栈数量
     */
    public  function info(string $msg, array $params = [], int $backtraceNum = 0)
    {
        $this->add('info', $msg ,$params, $backtraceNum);
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
        $data['uri'] = $request->getUri(); // uri
        $data['http_code'] = http_response_code(); // 相应状态码
        $data['method'] = $request->method(); // 请求方法
        $data['host_protocol'] = $request->getSchemeAndHttpHost(); // 主机
        $data['action'] = var_export($request->route()->getAction(), true); // 请求路由
//        $data['request_body'] = var_export($request->all(), true); // 请求体--参数
        $data['content_type'] = $request->header('content-type'); // 内容类型
        $data['ip'] = IpTool::get_client_ip_proxy_support();; // 主机ip
        $data['referer'] = $request->server('HTTP_REFERER'); // 请求缘由--可以做防盗链，也可以伪造，所以不太可信
        $data['cookie'] = var_export($request->cookies->all(), true); // cookie
        $data['token'] = $request->header('token'); // 令牌
        $data['user_agent'] = $request->userAgent(); // 用户代理
        /**
         * 拼接内容
         */
        $log = '';
        foreach ($data as $k => $item) {
            $log .= "\t". '{'. $k . ' => ' . ($item ?? '#') . '}';
        }

        $this->add('info', $log , $request->all());
    }


    /**
     * @Function: add
     * @Notes: 加入日志写入队列
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-20  17:45
     *
     * @param string $tag
     * @param string $msg
     *
     * @param array $params
     * @param int $backtraceNum
     */
    private  function add(string $tag, string $msg, array $params, int $backtraceNum = 0)
    {
        if (count($params) > 0) {
            $params = var_export($params,true);
        } else {
            $params = '';
        }
        // 记录调用栈数量
        if ($backtraceNum > 0) {
            // 去除当前方法栈记录
            $backtrace = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $backtraceNum + 1), 1);
            $msg .= "调用栈: " . var_export($backtrace, true);
            unset($backtrace);
        }
        $microtime = (string)microtime(true);
        $microtime = substr($microtime, strpos($microtime, '.') + 1, 4);
        $time = date('Y-m-d H:i:s.') . $microtime;
        $msg = $this->flag . "[$time] $msg 参数：";
        $this->messages[$tag][] = $msg . $params . "\r\n";
    }

    /**
     * 写入文件
     */
    public function write()
    {
        LogWriter::init()->write('system_log', $this->messages);
    }

}
