<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/12/1
 * Time: 上午10:03
 */

define('APP_ENV', getenv('WEB_ENV') ? getenv('WEB_ENV') : 'test');
define('CRONTAB_PATH', __DIR__.DIRECTORY_SEPARATOR);
define('CONFIG_PATH',CRONTAB_PATH.'config'.DIRECTORY_SEPARATOR.'env_'.APP_ENV.DIRECTORY_SEPARATOR);
require_once CRONTAB_PATH."vendor".DIRECTORY_SEPARATOR."autoload.php";
$mysqlConfig = include CONFIG_PATH."mysql.php";
\library\wuti\shard\mysqlShard::init($mysqlConfig);
