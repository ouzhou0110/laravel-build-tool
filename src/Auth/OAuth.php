<?php


namespace ZhouOu\LaravelTool\Auth;

use ZhouOu\LaravelTool\Log\SystemLog;
use ZhouOu\LaravelTool\Tool\ConfigTool;
use ZhouOu\LaravelTool\Tool\ReturnInfoTool;

class OAuth
{
    /**
     * @Function: login
     * @Notes: 登录
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  10:02
     *
     * @param string $key
     * @param array $params
     * @param int $time
     *
     * @return array
     *
     * @throws \Exception
     */
    public function login(string $key, array $params = [], int $time = 7200)
    {
        // 配置检测
        $ret = ConfigTool::check(['auth.auth_key', 'auth.auth_mode']);
        if ($ret['code'] == 0) {
            // 失败
            return $ret;
        }

        // 获取配置
        $authMode = ConfigTool::get('zhouOuConfig')['auth'];
        $authKey = $authMode['auth_key'];
        $authMode = $authMode['auth_mode'];

        $key = bcrypt(bcrypt(time() . $key . random_bytes(20)));
        session([
            $key => $params,
        ]);
        if ($authMode === 'cookie') {
            $data = ['mode' => 'cookie', 'value' => \Cookie::make($authKey, $key)];
            return ReturnInfoTool::msg(1, 'cookie生成成功', $data);
        }
        $data = ['mode' => 'token',
            'value' => ['token' => [
                'tokenName' => $authKey,
                'tokenValue' => $key
            ]]];
        return ReturnInfoTool::msg(1, 'token生成成功', $data);
    }

    /**
     * @Function: logout
     * @Notes: 退出登录
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  14:04
     *
     *
     * @return bool|void
     *
     */
    public function logout()
    {
        $key = $this->getKey();
        if ($key == false || $key == NULL) {
            return true;
        }
        return session()->forget($key);
    }

    /**
     * @Function: check
     * @Notes: 检测是否登录
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  14:06
     *
     *
     * @return bool
     *
     */
    public function check()
    {
        $key = $this->getKey();
        if ($key == false || $key == NULL) {
            return true;
        }
        return session($key) ? true : false;
    }


    /**
     * @Function: getId
     * @Notes: 获取id
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  13:55
     *
     * @param string $id
     *
     * @return array|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     *
     */
    public function getId(string $id = 'id')
    {
        return $this->get($id);
    }

    /**
     * @Function: getName
     * @Notes: 获取用户名称
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  13:56
     *
     * @param string $name
     *
     * @return array|\Illuminate\Session\SessionManager|\Illuminate\Session\Store|mixed
     *
     */
    public function getName(string $name = 'name')
    {
        return $this->get($name);
    }

    /**
     * @Function: get
     * @Notes: 获取session中想要的字段
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  13:50
     *
     * @param string $name 需要获取的键
     * @return mixed
     */
    public function get(string $name)
    {
        $key = $this->getKey();
        if ($key == false || $key == NULL) {
            return NULL;
        }
        $fields = session($key);

        if ($fields && isset($fields[$name])) {
            return $fields[$name];
        }
        return NULL;
    }

    /**
     * @Function: getKey
     * @Notes: 获取session的key
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2020-03-21  14:00
     *
     *
     * @return bool|string|null
     *
     */
    private function getKey()
    {
        // 配置检测
        $ret = ConfigTool::check(['auth.auth_key', 'auth.auth_mode']);
        if ($ret['code'] == 0) {
            // 失败
            return false;
        }

        // 获取配置
        $authMode = ConfigTool::get('zhouOuConfig')['auth'];
        $authKey = $authMode['auth_key'];
        $authMode = $authMode['auth_mode'];

        if($authMode == 'cookie') {
            $key = request()->cookie($authKey);
        } else {
            $key = request()->header($authKey);
        }
        return $key;
    }


    public function test()
    {
        echo 'hello world';
    }
}
