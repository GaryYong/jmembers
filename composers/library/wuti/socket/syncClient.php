<?php

namespace library\wuti\socket;

/**
 * socket连接处理发送类，基于swoole_client同步实现
 */
class syncClient
{
	const CONNECT_ERROR = -1;
	const SEND_ERROR = -2;
	const RECEV_ERROR = -3;
	const PACKAGE_ERROR = -4;

	/**
	 * 最后一次socket错误状态
	 * @var type
	 */
	private $lastSocketErrCode = 0;

	/**
	 * 当前状态,0:初始化状态, -1:连接失败，-2：发送失败，-3：接收失败
	 * @var type
	 */
	private $currentStatus = 0;

	protected $ip, $port, $protocol, $time_out;

	/**
	 * swoole_client
	 * @var \swoole_client
	 */
	public $swooleClient = FALSE;


	public function __construct($ip, $port, $protocol = NULL, $time_out = 2)
	{
		$this->ip = $ip;
		$this->port = $port;
		if (is_array($protocol)) {
			$protocol['open_tcp_nodelay'] = TRUE;
		}
		$this->protocol = $protocol;
		$this->time_out = $time_out;
	}


	/**
	 * 设置状态
	 * @param type $status
	 * @param type $unsetClient
	 */
	protected function setStatus($status, $unsetClient = TRUE, $errCode = FALSE)
	{
		$this->lastSocketErrCode = $errCode === FALSE ? $this->swooleClient->errCode : $errCode;
		$this->currentStatus = $status;
		$unsetClient && $this->swooleClient = FALSE;
	}


	/**
	 * 获取状态
	 * @return array
	 */
	public function getStatus()
	{
		return [
			"errorCode" => $this->lastSocketErrCode,
			"status" => $this->currentStatus,
		];
	}


	public function close()
	{
		$this->setStatus(0, FALSE, 0);
		if ($this->swooleClient && $this->swooleClient->isConnected()) {
			$this->swooleClient->close();
		}
	}


	/**
	 * 连接服务
	 * @return boolean
	 */
	protected function connect()
	{
		$this->setStatus(0, FALSE, 0);
		if ($this->swooleClient && !$this->swooleClient->isConnected()) {
			$this->swooleClient = FALSE;
		}
		if (!$this->swooleClient) {
			$this->swooleClient = new \swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
			$this->protocol && $this->swooleClient->set($this->protocol);
			$rc = $this->swooleClient->connect($this->ip, $this->port, $this->time_out);
			if (!$rc) {
				$this->setStatus(self::CONNECT_ERROR);
				return FALSE;
			}
			//$socket = $this->swooleClient->getSocket();
			//socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
		}
		return TRUE;
	}


	/**
	 * 发送
	 * @param type $buff
	 * @param Boolean $try 断线重试次数，默认1次
	 */
	public function send($buff, $try = 1)
	{
		do {
			if ($this->connect()) {
				$ret = $this->swooleClient->send($buff);
				if (!$ret) {
					$this->setStatus(self::SEND_ERROR);
				} else {
					return TRUE;
				}
			}
			$try--;
		} while (in_array($this->currentStatus, [self::CONNECT_ERROR, self::SEND_ERROR]) && $try >= 0);
		return FALSE;
	}


	/**
	 * 获取响应数据
	 * @return string|bool
	 */
	public function recv()
	{
		$this->setStatus(0, FALSE, 0);
		$data = $this->swooleClient->recv();
		if ($data) {
			return $data;
		}
		$this->setStatus(self::RECEV_ERROR, TRUE);
		return FALSE;
	}


	/**
	 * 发送并接收
	 * @param string $buff
	 * @param Boolean $try 断线重试次数，默认不重试
	 * @param Boolean $repeat swoole_client在极端情况下会因为系统中断导致接收数据出问题，当前通过休眠重试来解决。默认50次
	 */
	public function sendAndRecv($buff, $try = 0)
	{

		do {
			$time = time();
			if ($this->send($buff, 1)) {
				$ret = $this->recv();
				if ($ret !== FALSE) {
					return $ret;
				}
				$uTime = time() - $time;
				if ($uTime > 1) {
					return $ret;
				}
			}
			$try--;
		} while ($try >= 0);
		$this->swooleClient = FALSE;
		return FALSE;
	}

}