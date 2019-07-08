<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/12/1
 * Time: 上午10:40
 */

return [
    [
        'title' => '总览',
        'call' => 'Overview',
        'params' => ['day'=>-1],
        //分钟，小时，天，月，周
        'crontab' => ['10','1','*','*','*']
    ],
    [
        'title' => '总览-自尊达人',
        'call' => 'Overview2',
        'params' => ['day'=>-1],
        //分钟，小时，天，月，周
        'crontab' => ['10','2','*','*','*']
    ]
];