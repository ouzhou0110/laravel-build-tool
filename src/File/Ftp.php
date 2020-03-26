<?php


namespace ZhouOu\LaravelTool\File;

use Exception;
use ZhouOu\LaravelTool\Tool\ConfigTool;

class FTP
{
    /**
     * ftp连接
     */
    public $ftp = null;

    /**
     * 连接参数
     */
    private $params = [];

    /**
     * FTP constructor.
     * @param string $host 主机
     * @param string $user 账号
     * @param string $password 密码
     * @param int $port 端口
     * @param int $timeout 连接等待时间
     */
    public function __construct(string $host = '', string $user = '', string $password = '', int $port = 21, int $timeout = 90)
    {
        if (strlen($host) > 1) {
            $this->params['host'] = $host;
            $this->params['user'] = $user;
            $this->params['password'] = $password;
            $this->params['port'] = $port;
            $this->params['timeout'] = $timeout;
        } else {
            $ftpConfig = ConfigTool::get('zhouOuConfig')['ftp'];
            $this->params['host'] = $ftpConfig['ftp_host'];
            $this->params['user'] = $ftpConfig['ftp_name'];
            $this->params['password'] = $ftpConfig['ftp_password'];
            $this->params['port'] = $ftpConfig['ftp_port'];
            $this->params['timeout'] = $ftpConfig['ftp_timeout'];
        }
    }

    /**
     * @Function: connect
     * @Notes: 连接
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  9:57
     *
     * @throws \Exception
     */
    public function connect()
    {
        // 连接
        $this->ftp = ftp_connect($this->params['host'], $this->params['port'], $this->params['timeout']);
        if ($this->ftp === false) {
            throw new \Exception("ftp连接失败");
        }
        // 登录
        $ret = ftp_login($this->ftp, $this->params['user'], $this->params['password']);
        if ($ret === false) {
            throw new \Exception('ftp登录失败');
        }
        // 建立被动模式
        $ret = ftp_pasv($this->ftp, true);
        if ($ret === false) {
            throw new \Exception('ftp被动建立连接失败');
        }
        return true;
    }


    /**
     * @Function: get
     * @Notes:
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  10:08
     *
     * @param string $remotePath 服务器地址
     * @param string $localPath 本地地址
     * @param int $mode
     *
     * @return bool
     * @throws Exception
     */
    public function get(string $remotePath, string $localPath, int $mode = FTP_BINARY)
    {
        // 检测文件目录是否存在
        if (!is_file($localPath)) {
            $dir = dirname($localPath);
            if (!$this->_mkdirLocal($dir)) {
                throw new Exception("创建本地文件夹失败：$dir");
            }
        }
        $localPath = $this->_formatPath($localPath, 2);
        $remotePath = $this->_formatPath($remotePath, 1);
        $ret = ftp_get($this->ftp, $localPath, $remotePath, $mode);
        return $ret;
    }

    /**
     * @Function: put
     * @Notes: 上传文件
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  10:43
     *
     * @param string $remoteDir 远程文件夹路径
     * @param string $remoteFile 远程文件名
     * @param string $localFile 本地文件路径
     * @return bool
     */
    public function put(string $remoteDir, string $remoteFile, string $localFile)
    {
        $localFile = $this->_formatPath($localFile, 2);
        $remoteDir = $this->_formatPath($remoteDir, 1);
        if (!$this->_mkdirRemote($remoteDir)) {
            return false;
        }
        // 选择文件路径
        $ret = @ftp_chdir($this->ftp, $remoteDir);
        if (!$ret) {
            return false;
        }
        // 开启被动模式-不懂，如果此时不开被动模式也可以上传成功呀？？
        $ret = ftp_pasv($this->ftp, true);
        if (!$ret) {
            return false;
        }
        // 上传文件
        $ret = ftp_put($this->ftp, $remoteFile, $localFile, FTP_BINARY);
        if (!$ret) {
            return false;
        }
        return true;
    }


    /**
     * @Function: delete
     * @Notes: 删除文件
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  10:58
     *
     * @param string $remoteFilePath 远程文件地址
     *
     * @return bool
     *
     */
    public function delete(string $remoteFilePath)
    {
        $remoteFilePath = $this->_formatPath($remoteFilePath);
        if (ftp_delete($this->ftp, $remoteFilePath)) {
            return true;
        }
        return false;
    }

    /**
     * @Function: getList
     * @Notes: 获取目录文件列表
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  13:33
     *
     * @param string $remotePath 远程地址
     * @return array|bool
     */
    public function getList(string $remotePath)
    {
        $remotePath = $this->_formatPath($remotePath);
        $list = ftp_nlist($this->ftp, $remotePath);
        return $list;
    }

    /**
     * @Function: close
     * @Notes: 关闭连接
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  9:59
     *
     */
    public function close()
    {
        if ($this->ftp != null) {
            ftp_close($this->ftp);
            $this->ftp = null;
        }
    }

    /**
     * 关闭连接
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * @Function: _mkdirLocal
     * @Notes: 创建文件夹
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  10:12
     *
     * @param string $dir 文件夹路径
     *
     * @return bool
     *
     */
    private function _mkdirLocal(string $dir)
    {
        if (is_dir($dir)) {
            return true;
        }
        return mkdir($dir, 0777, true);
    }

    /**
     * @Function: _mkdirRemote
     * @Notes: ftp创建文件夹
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  10:43
     *
     * @param string $dir
     * @param int $mode
     *
     * @return bool
     *
     */
    private function _mkdirRemote(string $dir, int $mode = 0777)
    {
        // 检测路径是否存在
        if (@ftp_chdir($this->ftp, $dir)) {
            return true;
        }

        // 不存在，一层一层创建目录
        $dir = str_replace('\\', '/', $dir);
        $dir = rtrim($dir, '/');
        $dirArr = explode('/', $dir);
        $count = count($dirArr);
        $path = '';
        for ($i = 0; $i < $count; $i++) {
            $path .= "/{$dirArr[$i]}";
            if (@ftp_chdir($this->ftp, $path)) {
                continue;
            }
            @ftp_chdir($this->ftp, "/");
            if (!@ftp_mkdir($this->ftp, $path)) {
                return false;
            }
            @ftp_chmod($this->ftp, $mode, $path);
        }

        return true;
    }

    /**
     * @Function: _formatPath
     * @Notes: 格式化地址
     * @Author: zhou.ou
     * @Email: <zhou.ou@starcor.com>
     * @Date: 2019-12-31  11:25
     *
     * @param string $path
     * @param int $mode 1:linux 2:windows
     *
     * @return string
     */
    private function _formatPath(string $path, $mode = 1): string
    {
        if ($mode == 1) {
            $search = '\\';
            $replace = '/';
        } else {
            $search = '/';
            $replace = '\\';
        }

        return str_replace($search, $replace, $path);
    }
}

