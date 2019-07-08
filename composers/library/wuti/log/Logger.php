<?php
/**
 * Created by PhpStorm.
 * User: gary.li<1031965173@qq.com>
 * Date: 2018/5/24 0024
 * Time: 15:01
 */
namespace library\wuti\log;
use library\wuti\log\Log;

class Logger{
    private static $_instance = null;
    private static $level = 4;
    private static $logHandler = null;

    /**
     * @param $config ['level'=>1,'file_path'=>'','file_max_size'=>1,'log_type'=>'file']
     */
    public static function init($config){
        if(!self::$_instance){
            self::$_instance = new Logger($config);
        }
    }

    private function __construct($config)
    {
        $config['log_type'] = isset($config['log_type']) ? $config['log_type'] : 'file';
        switch ($config['log_type']){
            case 'file':
                self::$logHandler = new \library\wuti\log\LogFile($config['file_path'],$config['file_max_size']);
                break;
        }
        self::$level = isset($config['level']) ? $config['level'] : self::$level;
    }

    public static function log($msg,$filename){
        if(self::$logHandler && self::$level >= Log::NOTICE_LEVEL){
            self::$logHandler->notice($msg,$filename);
        }
    }

    public static function error($msg,$filename){
        if(self::$logHandler && self::$level >= Log::ERROR_LEVEL){
            self::$logHandler->error($msg,$filename);
        }
    }

    public static function warning($msg,$filename){
        if(self::$logHandler && self::$level >= Log::WARNING_LEVEL){
            self::$logHandler->warning($msg,$filename);
        }
    }

    public static function notice($msg,$filename){
        if(self::$logHandler && self::$level >= Log::NOTICE_LEVEL){
            self::$logHandler->notice($msg,$filename);
        }
    }

    public static function debug($msg,$filename){
        if(self::$logHandler && self::$level >= Log::DEBUG_LEVEL){
            self::$logHandler->debug($msg,$filename);
        }
    }
}
