<?php
return array(
    'api' => array(
        'domains' => array("http://test.gamecpl.com"=>'本地',"http://193.112.30.107"=>'外网'),
        'method' => array(
            array(
                'opt_name' => '用户信息',
                'opt_data' => array(
                    array('url'=>'/api/Users/check_exists','name'=>'检查是否安装过','param'=>'{"imei":"221e030fd5b38e68f9182ecabc8c58bf"}'),
                    array('url'=>'/api/Users/show','name'=>'通过用户id查询信息','param'=>'{"user_id":100227,"token":"UFZU3NTPoM"}'),
                    array('url'=>'/api/Users/device_show','name'=>'通过设备码查询信息','param'=>'{"imei":"221e030fd5b38e68f9182ecabc8c58bf","platform":1,"token":"UFZU3NTPoM"}')
                )
            )
        )
    )
);