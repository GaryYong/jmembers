<?php

namespace library\wuti\socket;

use swoole_client;
/**
 * 处理异步client
 *
 * @author JsonChen
 */
class asyncClient {

	/**
	 * swoole_client
	 * @var swoole_client 
	 */
	protected $client = false;
	//连接的ip
	public $ip, $port, $protocol;
	//是否为客户端主动发起关闭
	public $client_close = false;
	//是否触发过on_connect
	protected $is_onConnect = false;
	//是否触发过 onclose
	protected $is_onClose = false;

	public function __construct($ip, $port, $protocol) {
		$this->ip = $ip;
		$this->port = $port;
		$this->protocol = $protocol;
	}

	public function __destruct() {
		
	}

	/**
	 * 关闭连接并减计数器\同时释放资源
	 */
	public function doClose() {
		$this->client_close = true;
		if ($this->is_onConnect) {
			if ($this->client && $this->client->isConnected()) {
				$this->client->close();
			}
			//释放
			$this->client = NULL;
		}
	}

	/**
	 * 发送数据，如果发送不成功，则直接关闭连接
	 * @param type $data
	 * @return type
	 */
	public function doSend($data) {
		$code = 0;
		if ($this->client && $this->client->isConnected()) {
			if ($this->client->send($data)) {
				return true;
			} else {
				$code = $this->client->errCode;
			}
		}
		if ($this->is_onConnect) {
			$this->doClose();
		}
		return false;
	}

	/**
	 * 服务端触发-关闭
	 * @param type $closeType
	 * @return type
	 */
	protected function onClose() {
		$this->client_close = true;
		$this->client = NULL;
	}

	protected function onConnectedGame() {
		$this->is_onConnect = true;
		if ($this->client_close) {
			//链接已被客户端强制中断，此次关闭释放资源
			if ($this->client) {
				$this->client->close();
				$this->client = NULL;
			}
		}
	}

	/**
	 * 连接gameserver
	 */
	public function Connect() {
		$this->client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
		$this->client->set($this->protocol);
		$this->client->on('connect', function (swoole_client $socket) {
			$this->onConnectedGame();
		});
		$this->client->on('error', function (swoole_client $socket) {
			$this->onClose();
		});
		$this->client->on('close', function (swoole_client $socket) {
			$this->is_onClose = true;
			$this->onClose();
		});
		$this->client->on('receive', function (swoole_client $socket, $data) {
			
		});
		$ret = $this->client->connect($this->ip, $this->port, 10);
		if (!$ret) {
			$this->doClose();
			return true;
		}
		return false;
	}

}
