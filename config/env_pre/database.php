<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

return [
    'db_admin' =>[
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '127.0.0.1',
        // 数据库名
        'database'       => 'gm',
        // 用户名
        'username'       => 'logcpl',
        // 密码
        'password'       => 'CYnvzQlDZ1YbdA7p',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => 'gm_',
        // 数据库调试模式
        'debug'          => false,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => false,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    'db_gamelog' =>[
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '111.230.206.71',
        // 数据库名
        'database'       => 'gamelog',
        // 用户名
        'username'       => 'admin',
        // 密码
        'password'       => 'Los08k#$(P',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => 'gm_',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    'db_gamelog_zzdr' =>[//至尊达人斗地主log
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '111.230.206.71',
        // 数据库名
        'database'       => 'gamecpl_zzdr',
        // 用户名
        'username'       => 'admin',
        // 密码
        'password'       => 'Los08k#$(P',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => '',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    'db_gamelog_readpack' =>[//至尊达人斗地主log
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '134.175.156.119',
        // 数据库名
        'database'       => 'gamelog',
        // 用户名
        'username'       => 'logcpl',
        // 密码
        'password'       => 'CYnvzQlDZ1YbdA7p',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => '',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    'db_gamecpl_readpack' =>[//至尊达人斗地主log
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '134.175.156.119',
        // 数据库名
        'database'       => 'gamecpl',
        // 用户名
        'username'       => 'logcpl',
        // 密码
        'password'       => 'CYnvzQlDZ1YbdA7p',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => '',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    'db_gamecpl_zft' =>[//疯狂炸翻天
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '106.52.214.85',
        // 数据库名
        'database'       => 'gamecpl',
        // 用户名
        'username'       => 'logcpl',
        // 密码
        'password'       => '#74ldIF#!CP',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => '',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    'db_gamelog_zft' =>[//疯狂炸翻天
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '106.52.214.85',
        // 数据库名
        'database'       => 'gamelog',
        // 用户名
        'username'       => 'logcpl',
        // 密码
        'password'       => '#74ldIF#!CP',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => '',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    'db_gamecpl_zft' =>[//疯狂炸翻天log
        // 数据库类型
        'type'           => 'mysql',
        // 服务器地址
        'hostname'       => '106.52.214.85',
        // 数据库名
        'database'       => 'gamecpl',
        // 用户名
        'username'       => 'logcpl',
        // 密码
        'password'       => '#74ldIF#!CP',
        // 端口
        'hostport'       => '3306',
        // 连接dsn
        'dsn'            => '',
        // 数据库连接参数
        'params'         => [],
        // 数据库编码默认采用utf8
        'charset'        => 'utf8',
        // 数据库表前缀
        'prefix'         => '',
        // 数据库调试模式
        'debug'          => true,
        // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
        'deploy'         => 0,
        // 数据库读写是否分离 主从式有效
        'rw_separate'    => false,
        // 读写分离后 主服务器数量
        'master_num'     => 1,
        // 指定从服务器序号
        'slave_no'       => '',
        // 是否严格检查字段是否存在
        'fields_strict'  => true,
        // 数据集返回类型 array 数组 collection Collection对象
        'resultset_type' => 'collection',
        // 是否自动写入时间戳字段
        'auto_timestamp' => false,
        // 是否需要进行SQL性能分析
        'sql_explain'    => false,
    ],
    // 数据库类型
    'type'           => 'mysql',
    // 服务器地址
    'hostname'       => '127.0.0.1',
    // 数据库名
    'database'       => 'gm',
    // 用户名
    'username'       => 'logcpl',
    // 密码
    'password'       => 'CYnvzQlDZ1YbdA7p',
    // 端口
    'hostport'       => '3306',
    // 连接dsn
    'dsn'            => '',
    // 数据库连接参数
    'params'         => [],
    // 数据库编码默认采用utf8
    'charset'        => 'utf8',
    // 数据库表前缀
    'prefix'         => 'gm_',
    // 数据库调试模式
    'debug'          => false,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'         => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'    => false,
    // 读写分离后 主服务器数量
    'master_num'     => 1,
    // 指定从服务器序号
    'slave_no'       => '',
    // 是否严格检查字段是否存在
    'fields_strict'  => false,
    // 数据集返回类型 array 数组 collection Collection对象
    'resultset_type' => 'collection',
    // 是否自动写入时间戳字段
    'auto_timestamp' => false,
    // 是否需要进行SQL性能分析
    'sql_explain'    => false
];
