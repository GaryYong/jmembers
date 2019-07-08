<?php
namespace library\wuti\socket;

use library\wuti\protocols\rpcPackage;
use library\wuti\socket\syncClient;

class rpcClient{
	/**
	 * syncclient
	 * @var syncClientBase 
	 */
	public $syncClient = false;
	private $ip, $port, $isudp;

	public function __construct($ip, $port, $isudp = false){
		$this->ip = $ip;
		$this->port = $port;
		$this->isudp = $isudp;
		if(!$isudp){
			$protocol = array(
				'open_length_check' => true,
				'package_length_type' => 'N',
				'package_length_offset' => 9,
				'package_body_offset' => 13,
				'package_max_length' => 1024 * 64, //最大支持-512K数据
			);
			$this->syncClient = new syncClient($ip, $port, $protocol);
		}
	}

	private $ack = 0;

	private function requestTcp($buff, $response){
		try{
			$ret = $this->syncClient->send($buff);
			if($response){
				$ret = $this->syncClient->recv();
				if(!$ret){
					return false;
				}
				$data = rpcPackage::readRpcResponse($ret);
				if($data && ($data['head']['Ack'] == $this->ack)){
					if($data['code'] !== 0){
						throw new \Exception($data['result'], $data['code']);
					}
					return $data['result'];
				}else{
					throw new \Exception("not recev data");
				}
			}
		}catch(\Exception $ex){
			$this->syncClient->close();
			throw new \Exception($ex->getMessage(), $ex->getCode());
		}catch(\Throwable $ex){
			$this->syncClient->close();
			throw new \Exception($ex->getMessage(), $ex->getCode());
		}
	}

	/**
	 * 通过rpc服务请求数据
	 * @param string $api 如api.getList
	 * @param array $args 约定的参数
	 * @param bool $response 是否需有等待返回值
	 * @param type $version 版本号
	 * @return boolean
	 */
	public function requestRpc($api, $args, $response = true, $version = 1,$async=false){
		$this->ack++;
		if($this->isudp){
			$response = false; //udp默认不返回数据
		}
		$buff = rpcPackage::writeRpcRequest($this->ack, $api, $args, $version, $response,$async);
		if($this->isudp){
			return \library\wuti\functions::udp($this->ip, $this->port, $buff);
		}else{
			return $this->requestTcp($buff, $response);
		}
	}

}