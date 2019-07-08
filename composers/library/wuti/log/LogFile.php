<?php
/**
 * Created by PhpStorm.
 * User: gary.li<1031965173@qq.com>
 * Date: 2018/5/24 0024
 * Time: 10:58
 */

namespace library\wuti\log;

class LogFile extends Log
{
    private $file_path;
    private $file_max_size;

    public function __construct($file_path,$file_max_size)
    {
        $this->file_path = $file_path;
        $this->file_max_size = $file_max_size;
    }

    public function __set($name, $value)
    {
        // TODO: Implement __set() method.
        $this->$name = $value;
    }

    public function write($msg,$filename,$level){
        $logDate = date("Y-m-d H:i:s");
        $file_ext = ".log";
        $filename = $this->file_path.$filename;
        $pathInfo = pathinfo($filename);

        if(!is_dir($pathInfo['dirname'])){
            is_writable($pathInfo['dirname']) && mkdir($pathInfo['dirname'],0777,true);
        }

        //单个文件大小
        if($this->file_max_size && is_file($filename.$file_ext) && filesize($filename.$file_ext) > ($this->file_max_size*1024*1024)){
            $filename .= date('_G');
        }

        if($msg instanceof \Exception){
            $msg = $msg->getMessage();
        }

        $msg = "[{$logDate}][{$level}]{$msg}\r\n";
        @file_put_contents($filename.$file_ext,$msg,FILE_APPEND);
    }

    public function error($msg, $filename)
    {
        // TODO: Implement error() method.
        $this->write($msg,$filename,$this->getLevelHumanName(self::ERROR_LEVEL));
    }

    public function warning($msg, $filename)
    {
        // TODO: Implement warning() method.
        $this->write($msg,$filename,$this->getLevelHumanName(self::WARNING_LEVEL));
    }

    public function notice($msg, $filename)
    {
        // TODO: Implement notice() method.
        $this->write($msg,$filename,$this->getLevelHumanName(self::NOTICE_LEVEL));
    }

    public function debug($msg, $filename)
    {
        // TODO: Implement debug() method.
        $this->write($msg,$filename,$this->getLevelHumanName(self::DEBUG_LEVEL));
    }
}
