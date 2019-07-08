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
		'toyblast' => [
			//主库配置，主要用于写
			'master' => [
				//表示0~5 5个库使用同一个mysql实例，配置信息如下
				'0-5' => [
					'hostname' => '192.168.143.67',
					'port' => 3306,
					'username' => 'root',
					'password' => 'root',
					'charset' => 'utf-8',
				],
				'6-10' => [
					'hostname' => '192.168.143.67',
					'port' => 3306,
					'username' => 'root',
					'password' => 'root',
					'charset' => 'utf-8',
				],
			],
			//从库，主要用于读，示业务而定，
			'slave' => [
				'0-5' => [
					'hostname' => '192.168.143.67',
					'port' => 3306,
					'username' => 'root',
					'password' => 'root',
					'charset' => 'utf-8',
				],
				'6-10' => [
					'hostname' => '192.168.143.67',
					'port' => 3306,
					'username' => 'root',
					'password' => 'root',
					'charset' => 'utf-8',
				],
			],
		],
		//同上
		'toyblastBackend' => [
			'master' => [
				'0-0' => [
					'hostname' => '192.168.143.67',
					'port' => 3306,
					'username' => 'root',
					'password' => 'root',
					'charset' => 'utf-8',
				],
			],
			'slave' => [
				'0-0' => [
					'hostname' => '192.168.143.67',
					'port' => 3306,
					'username' => 'root',
					'password' => 'root',
					'charset' => 'utf-8',
				],
			],
		],

	],
	//分库分表配置规则
	'shardConfig' => [
		//配置名称，对应mysqlFactory中的$targetConfigName
		'toyblast_frontend' => [
			'database' => [0, 10],//数据库分库规则，表示通过10个库求模，散列到0~10后缀的库中，且支持配置 [5,10],表示通过(10-5)个库求模，并散列到5-10对应的库中
			'tables' => [0, 10],//同上，
			'database_prefix' => 'toyblast',//对应shardHost在数据库配置
		],
		//同上
		'toyblast_backend' => [
			'database' => FALSE,//配置 flase,表示不需要分库，只有单库，获取对应数据库中 0-0 配置
			'tables' => FALSE,//同上，直接返回表名，不会追加表后缀
			'database_prefix' => 'toyblastBackend',
		],
	],
];