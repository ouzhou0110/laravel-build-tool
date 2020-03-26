<?php

return [
    ################### 通用配置 ##########################

    /**
     * 是否将api模式的格式化响应方法注入controller.php中
     * true：注入
     * false：不注入
     */
    'is_inject_api_response' => true,

    /**
     * 是否开启输出调试日志，生产环境必须关闭
     * 默认为：false
     */
    'is_open_print_debug_info' => true,


    /**
     * 是否使用当前包的系统日志
     * 默认为：true
     * 每次更新需要使用 composer dumpautoload 重新加载
     */
    'is_open_log' => false,


    ################## artisan table配置 #########################

    /**
     * 默认表名前缀-功能暂未实现
     */
    'tablePrefix' => '',

    /**
     * table组件的命名空间-model、action、service、query文件夹的上级命名空间
     * 比如：table命名空间为 App\Databases，那么model命名空间为 App\Databases\Models,
     * 采用 psr-4 规范，命名空间和路径保持一致
     */
    'tableNamespace' => 'App\Databases',

    /**
     * 使用zhouou:table命令时生成的组件
     */
    'tableInit' => [
        'model' => false,
        'action' => false,
        'query' => false,
        'logic' => true,
    ],


    ################### 日志模块配置 ########################

    /**
     * 系统日志(运行日志)管理，主要是laravel运行错误，或者代码错误；非手动添加日志
     */
    'system_log' => [
        /**
         * 是否在捕获到异常时，立即写入文件，建议为false
         * 设置为true，会有大量的os操作，耗性能
         * 默认为：false
         */
        'log_limit_write_enable' => false,


        /**
         * 日志文件最大文件大小--写入前判断，最终大小有浮动
         * 单位：M
         * 如果日志需求量大，建议设置为100m，且切割时间设置小一点
         * 最大100m，超过将以100m处理
         */
        'log_file_max_size' => 30,

        /**
         * 每个多少分钟切割一下日志文件
         * 单位：d:天，h：小时，m:分钟
         * 最多一天，配置大于一天不报错，但日志还是按一天切割
         */
        'log_divide_time' => '20m',

        /**
         * 日志文件前缀
         * 默认为：log_
         */
        'log_prefix' => 'log_',


        /**
         * 日志文件后缀
         * 默认为：txt
         */
        'log_ext' => 'txt',

        /**
         * 系统错误日志默认根路径是：app
         * 默认配置为
         */
        'log_path' => '/storage/logs/system/',
    ],

    'debug_log' => [
        /**
         * 是否在捕获到异常时，立即写入文件，建议为false
         * 设置为true，会有大量的os操作，耗性能
         * 默认为：false
         */
        'log_limit_write_enable' => false,


        /**
         * 日志文件最大文件大小--写入前判断，最终大小有浮动
         * 单位：M
         * 如果日志需求量大，建议设置为100m，且切割时间设置小一点
         * 最大100m，超过将以100m处理
         */
        'log_file_max_size' => 30,

        /**
         * 每个多少分钟切割一下日志文件
         * 单位：d:天，h：小时，m:分钟
         * 最多一天，配置大于一天不报错，但日志还是按一天切割
         */
        'log_divide_time' => '20m',

        /**
         * 日志文件前缀
         * 默认为：log_
         */
        'log_prefix' => 'log_',


        /**
         * 日志文件后缀
         * 默认为：txt
         */
        'log_ext' => 'txt',

        /**
         * 系统错误日志默认根路径是：app
         * 默认配置为
         */
        'log_path' => '/storage/logs/debug/',
    ],

    ##################### 认证模块 ############################

    /**
     * auth 配置
     */
    'auth' => [
        /**
         * 登录认证方法，cookie认证，还是使用head加入token字段认证,
         * 建议使用cookie模式，因为postman如果使用token，很难配置header
         */
        'auth_mode' => 'cookie',

        /**
         * 认证字段的key，用于获取对应数据
         */
        'auth_key' => 'token-cookie',
    ],

    ########################## 文件模块 ###############################

    /**
     * 文件上传使用存储引擎：
     * local => 使用服务本身
     * ftp => 使用ftp服务器
     * 默认使用local，生产环境中尽量使用一种引擎。数据库储存时会将当前引擎的信息也存储
     * 已兼容混合使用情况。
     * 请使用
     */
    'file_upload_driver' => 'ftp',

    /**
     * 文件上传默认字段名称
     * 如何在上传接口中不传递标识名称，将自动使用这个
     */
    'file_upload_tag' => 'file',

    /**
     * file 配置，文件上传到本地服务器
     */
    'file' => [

        /**
         * 上传文件最大大小，根据需求合理设计
         * 参照 php.ini 配置
         * 单位：m
         */
        'file_max_size' => '20',

        /**
         * 允许上传的文件后缀
         */
        'file_allow_ext' => ['jpg', 'png', 'gif', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'txt'],

        /**
         * 同时上传文件上限，请结合 file_max_size 大小进行设计
         * 默认为10个
         */
        'file_max_num' => 10,

        /**
         * 文件上传路径
         */
        'file_save_path' => '/ftp',
    ],


    'ftp' => [
        /**
         * ftp服务器公网ip
         */
        'ftp_host' => '106.12.102.124',

        /**
         * ftp服务器端口
         */
        'ftp_port' => '21',

        /**
         * ftp用户名
         */
        'ftp_name' => 'fscftp',

        /**
         * ftp用户密码
         */
        'ftp_password' => '123456',

        /**
         * ftp最长等待数
         */
        'ftp_timeout' => 120,

        /**
         *
         */
        'ftp_file_path' => '/zhouou/file'
    ],
];
