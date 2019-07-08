<?php
/**
 * Created by PhpStorm.
 * User: gary.li<1031965173@qq.com>
 * Date: 2018/5/24 0024
 * Time: 10:35
 */
namespace library\wuti\log;

abstract class Log
{
    const ERROR_LEVEL  = 1;
    const WARNING_LEVEL = 2;
    const NOTICE_LEVEL = 3;
    const DEBUG_LEVEL = 4;
    public static $level = 4;
    //INFO,NOTICE,DEBUG,WARNING,ERROR
    abstract function error($msg,$filename);
    abstract function warning($msg,$filename);
    abstract function notice($msg,$filename);
    abstract function debug($msg,$filename);

    public function getLevelHumanName($level){
        $level_name = 'DEBUG';
        $info = array(
            self::ERROR_LEVEL => 'ERROR',
            self::WARNING_LEVEL => 'WARNING',
            self::NOTICE_LEVEL => 'NOTICE',
            self::DEBUG_LEVEL => 'DEBUG'
        );
        $level_name = isset($info[$level]) ? $info[$level] : $level_name;
        return $level_name;
    }
}

