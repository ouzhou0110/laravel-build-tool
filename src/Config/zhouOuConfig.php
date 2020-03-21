<?php

return [
    /*******************通用配置*********************/

    /**
     * 是否将api模式的格式化响应方法注入controller.php中
     * true：注入
     * false：不注入
     */
    'isInjectApiResponse' => true,

    /**
     * 是否开启输出调试日志，生产环境必须关闭
     * 默认为：false
     */
    'isOpenPrintDebugInfo' => true,


    /**
     * 是否使用当前包的系统日志
     * 默认为：true
     * 每次更新需要使用 composer dumpautoload 重新加载
     */
    'isOpenLog' => true,


    /********************table配置********************/

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


    /***********************日志模块**********************/

    /**
     * 系统日志(运行日志)管理，主要是laravel运行错误，或者代码错误；非手动添加日志
     */
    'systemLog' => [
        /**
         * 是否在捕获到异常时，立即写入文件，建议为false
         * 设置为true，会有大量的os操作，耗性能
         * 默认为：false
         */
        'logLimitWriteEnable' => false,


        /**
         * 日志文件最大文件大小--写入前判断，最终大小有浮动
         * 单位：M
         * 如果日志需求量大，建议设置为100m，且切割时间设置小一点
         * 最大100m，超过将以100m处理
         */
        'logFileMaxSize' => 30,

        /**
         * 每个多少分钟切割一下日志文件
         * 单位：d:天，h：小时，m:分钟
         * 最多一天，配置大于一天不报错，但日志还是按一天切割
         */
        'logDivideTime' => '3m',

        /**
         * 日志文件前缀
         * 默认为：log_
         */
        'logPrefix' => 'log_',


        /**
         * 日志文件后缀
         * 默认为：txt
         */
        'logExt' => 'txt',

        /**
         * 系统错误日志默认根路径是：app
         * 默认配置为
         */
        'logPath' => '/storage/logs/system/',
    ],

    'debugLog' => [
        /**
         * 是否在捕获到异常时，立即写入文件，建议为false
         * 设置为true，会有大量的os操作，耗性能
         * 默认为：false
         */
        'logLimitWriteEnable' => false,


        /**
         * 日志文件最大文件大小--写入前判断，最终大小有浮动
         * 单位：M
         * 如果日志需求量大，建议设置为100m，且切割时间设置小一点
         * 最大100m，超过将以100m处理
         */
        'logFileMaxSize' => 30,

        /**
         * 每个多少分钟切割一下日志文件
         * 单位：d:天，h：小时，m:分钟
         * 最多一天，配置大于一天不报错，但日志还是按一天切割
         */
        'logDivideTime' => '3m',

        /**
         * 日志文件前缀
         * 默认为：log_
         */
        'logPrefix' => 'log_',


        /**
         * 日志文件后缀
         * 默认为：txt
         */
        'logExt' => 'txt',

        /**
         * 系统错误日志默认根路径是：app
         * 默认配置为
         */
        'logPath' => '/storage/logs/debug/',
    ],

];
