<?php

namespace library\wuti\socket;

use library\wuti\protocols\rpcPackage;
use library\wuti\protocols\rpcPbPackage;
use library\wuti\socket\syncClient;

class rpcPbClient
{
	/**
	 * syncclient
	 * @var syncClientBase
	 */
	public $syncClient = FALSE;

	private $ip, $port;


	public function __construct($ip, $port,$timeOut=2)
	{
		$this->ip = $ip;
		$this->port = $port;
		$protocol = [
			'open_length_check' => TRUE,
			'package_length_type' => 'S',
			'package_length_offset' => 7,
			'package_body_offset' => 9,
			'package_max_length' => 1024 * 64, //最大支持-512K数据
		];
		$this->syncClient = new syncClient($ip, $port, $protocol,$timeOut);
	}


	private function requestTcp($serverType, $cmdType, $pbRequest)
	{
		try {
			$data = rpcPbPackage::encode($serverType, $cmdType, $pbRequest);
			$ret = $this->syncClient->sendAndRecv($data, 1);
			if (!$ret) {
				throw new \Exception(sprintf("recv异常-%s:%s,%s", $this->ip, $this->port,
					json_encode($this->syncClient->getStatus())), 99);
			}
			rpcPbPackage::decodeResponse($ret, $serverType, $cmdType, $pbContent);
			return $pbContent;
		} catch (\Exception $ex) {
			$this->syncClient->close();
			throw new \Exception($ex->getMessage(), $ex->getCode());
		} catch (\Throwable $ex) {
			$this->syncClient->close();
			throw new \Exception($ex->getMessage(), $ex->getCode());
		}
	}


	/**
	 * 通过rpc服务请求数据
	 * @param $serverType
	 * @param $cmdType
	 * @param $pbRequest
	 * @return bool|mixed
	 */
	public function requestRpc($serverType, $cmdType, $pbRequest)
	{
		return $this->requestTcp($serverType, $cmdType, $pbRequest);
	}

}