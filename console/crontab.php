<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/12/1
 * Time: 上午10:03
 */

require_once __DIR__.DIRECTORY_SEPARATOR."init.php";
define('LOG_PATH',CRONTAB_PATH.'logs'.DIRECTORY_SEPARATOR);

$crontabCfg = require_once CRONTAB_PATH.'config'.DIRECTORY_SEPARATOR.'common'.DIRECTORY_SEPARATOR.'crontab.config.php';

foreach ($crontabCfg as $idx=>$cfg){
    $cfg['idx'] = $idx;
    $class = "\\console\\job\\".$cfg['call'];
    new $class($cfg);
}


