<?php
namespace library\wuti\shard;

use library\wuti\cache\cacheRedis;

/**
 * redis操作工厂类 配置文件对应样例 docs\example\config\redis.inc.php
 */
class redisShard
{

	private static $shardConfigs;


	public static function init($shardConfig)
	{
		self::$shardConfigs = $shardConfig;
	}


	/**
	 * 根据分库规则，获取目标配置对应的redis连接信息
	 * @param $targetConfigName
	 * @param $shardId
	 * @return array
	 */
	public static function getHostByShard($targetConfigName, $shardId)
	{

		$hosts = self::$shardConfigs[$targetConfigName];
		$redisCnt = count($hosts);
		if ($redisCnt > 1) {
			$shardId = abs(crc32($shardId));
			$rid = $shardId % count($hosts);
			return $hosts[$rid];
		} else {
			return current($hosts);
		}
	}


	/**
	 * 获取配置对应的redis操作类
	 * @param $targetConfigName
	 * @param int $shardId
	 * @return \library\wuti\cache\cacheRedis
	 */
	public static function getRedis($targetConfigName, $shardId = 0, $serializer = FALSE)
	{
		static $redisCaches = [];
		$host = self::getHostByShard($targetConfigName, $shardId);
		$key = $host['host'] . ':' . $host['port'] . ':' . ($serializer ? 1 : 0);
		if (isset($redisCaches[$key])) {
			return $redisCaches[$key];
		}
		$redis = new cacheRedis($host['host'], $host['port'], $host['password'], 3, $serializer);
		$redisCaches[$key] = $redis;
		return $redis;
	}
}