<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace library\wuti\socket;

/**
 * Description of httpClient
 *
 * @author json.chen
 */
class httpClient{
	public static function request($ip, $port, $url, $post, $call_data, $call_func, $header = array(), $timeout = 5000){
		$header['User-Agent'] = 'Access-Server';
		$header['Content-Type'] = 'application/x-www-form-urlencoded';
		$cli = new \swoole_http_client($ip, $port, $timeout);
		$cli->on('error', function ($cli){
			\library\wuti\log\Logger::log(__FUNCTION__, __CLASS__);
		});
		$cli->post($url, $post, function ($cli)use($call_data){
			$call_func($cli->body, $cli->statusCode, $call_data);
		});
	}

}