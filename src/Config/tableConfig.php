<?php

return [

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
        'model' => true,
        'action' => false,
        'query' => false,
        'logic' => false,
    ],
];
