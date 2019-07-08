<?php
/**
 * Created by PhpStorm.
 * User: json.chen
 * Date: 2017/9/26
 * Time: 15:44
 */
//数据库配置示例
return [
	//数据库配置信息
	'shardHost' => [
		//数据库名前缀，然后根据求模，后面自动补加数字，如toyblast5
		'gamelog' => [
			//主库配置，主要用于写
			'master' => [
				//表示0~5 5个库使用同一个mysql实例，配置信息如下
				'0-0' => [
					'hostname' => '127.0.0.1',
					'port' => 3306,
					'username' => 'root',
					'password' => '',
					'charset' => 'utf-8',
				]
			],
			//从库，主要用于读，示业务而定，
			'slave' => [],
		],
        'gm' => [
            //主库配置，主要用于写
            'master' => [
                //表示0~5 5个库使用同一个mysql实例，配置信息如下
                '0-0' => [
                    'hostname' => '127.0.0.1',
                    'port' => 3306,
                    'username' => 'root',
                    'password' => '',
                    'charset' => 'utf-8',
                ]
            ],
            //从库，主要用于读，示业务而定，
            'slave' => [],
        ]
	],
	//分库分表配置规则
	'shardConfig' => [
		//配置名称，对应mysqlFactory中的$targetConfigName
		'gamelog_share' => [
			'database' => false,//数据库分库规则，表示通过10个库求模，散列到0~10后缀的库中，且支持配置 [5,10],表示通过(10-5)个库求模，并散列到5-10对应的库中
			'tables' => false,//同上，
			'database_prefix' => 'gamelog',//对应shardHost在数据库配置
		],
        'gm_share' => [
            'database' => false,//数据库分库规则，表示通过10个库求模，散列到0~10后缀的库中，且支持配置 [5,10],表示通过(10-5)个库求模，并散列到5-10对应的库中
            'tables' => false,//同上，
            'database_prefix' => 'gm',//对应shardHost在数据库配置
        ]
	],
];