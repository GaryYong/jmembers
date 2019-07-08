<?php
namespace library\wuti\shard;

/**
 * mysql数据库操作工厂类 配置文件对应样例 docs\example\config\mysql.inc.php
 */
class mysqlShard
{

	private static $shardConfigs;


	public static function init($shardConfig)
	{
		self::$shardConfigs = $shardConfig;
	}


	/**
	 * 返回取模的值
	 *
	 * @param Int $number
	 * @param Int $s
	 * @return Int Id
	 */
	private static function getModByNumber($number, $start, $end)
	{
		return intval($number % ($end + 1 - $start)) + $start;
	}


	/**
	 * 根据分库规则，获取目标配置对应的数据库连接信息
	 * @param $targetConfigName
	 * @param $shardId
	 * @param bool $master
	 * @return bool
	 */
	public static function getHostByShard($targetConfigName, $shardId, $master = TRUE)
	{
		$shardId = abs(crc32($shardId));
		$type = $master ? "master" : "slave";
		$shardConfig = self::$shardConfigs['shardConfig'][$targetConfigName];
		$dbPrefix = $shardConfig['database_prefix'];
		$hosts = self::$shardConfigs['shardHost'][str_replace('_share','',$targetConfigName)][$type];
		$database = $shardConfig['database'];
		if (is_array($database)) {
			$dbId = self::getModByNumber($shardId, current($database), end($database));
			$host = FALSE;
			foreach ($hosts as $key => $value) {
				list($s, $e) = explode('-', $key);
				if ($dbId >= (int) $s && $dbId <= (int) $e) {
					$host = $value;
					$host['database'] = $dbPrefix . $dbId;
					break;
				}
			}
		} else {
			$host = current($hosts);
			$host['database'] = $dbPrefix;
		}
		return $host;
	}


	/**
	 * 根据数据表分配规则，获取目标配置的表名
	 * @param $targetConfigName
	 * @param $shardId
	 * @param $tablePrefix
	 * @return string
	 */
	public static function getTableByShard($targetConfigName, $shardId, $tablePrefix)
	{
		$tables = self::$shardConfigs['shardConfig'][$targetConfigName]['tables'];
		if (is_array($tables)) {
			$tbId = self::getModByNumber($shardId, current($tables), end($tables));
			return $tablePrefix . $tbId;
		}
		return $tablePrefix;
	}


	/**
	 * 获取主库mysql连接操作处理类
	 * @param $targetConfigName
	 * @param $shardId
	 * @return \library\wuti\database\dbMysqli|mixed
	 */
	public static function getMasterMysqlByShard($targetConfigName, $shardId, &$dataBaseName)
	{
		$host = self::getHostByShard($targetConfigName, $shardId, TRUE);
		$dataBaseName = $host['database'];
		return self::getMysqlLink($host);
	}

	/**
	 * 返回用户数据库及表
	 * @param string $shardId
	 * @param string $tableName
	 * @return mixed
	 */
	public static function getDbAndTable($targetConfigName,$shardId, $tableName)
	{
		$mysql = self::getMasterMysqlByShard($targetConfigName, $shardId, $dataBaseName);
		$table = self::getTableByShard($targetConfigName, $shardId, $tableName);
		return [$mysql, $dataBaseName . '.' . $table];
	}

	/**
	 * 获取从库mysql连接操作处理类
	 * @param $targetConfigName
	 * @param $shardId
	 * @return \library\wuti\database\dbMysqli|mixed
	 */
	public static function getSlaveMysqlByShard($targetConfigName, $shardId)
	{
		$host = self::getHostByShard($targetConfigName, $shardId, FALSE);
		return self::getMysqlLink($host);
	}


	/**
	 * 获取数据库操作处理类
	 * @param $host
	 * @param bool $cache 默认为true,会缓存住连接，确保多次使用时不会创建连接
	 * @return \library\wuti\database\dbMysqli|mixed
	 */
	public static function getMysqlLink($host, $cache = TRUE)
	{
		static $mysqlLinks = [];
		$cache_key = $host['hostname'] . ':' . $host['port'];
		if ($cache) {
			if (isset($mysqlLinks[$cache_key])) {
				return $mysqlLinks[$cache_key];
			}
		}
		$mysqli = new \library\wuti\database\dbMysqli($host);
		$mysqlLinks[$cache_key] = $mysqli;
		return $mysqli;
	}
}


