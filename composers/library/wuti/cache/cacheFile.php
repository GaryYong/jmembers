<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/10/20
 * Time: 下午10:29
 */
namespace library\wuti\cache;

class cacheFile{
    private static $shareConfig;

    public static function init($config){
        self::$shareConfig = $config;
        $filePath = self::$shareConfig['file_path'];
        if(!is_dir($filePath)){
            mkdir($filePath,0777,true);
        }
        if(!is_writable($filePath)){
            throw new \Exception("{$filePath} is Not writable");
        }
    }

    public static function save($name,$content){
        self::checkInit();
        $fileName = self::$shareConfig['file_path'].$name;
        self::_checkDir($fileName);
        file_put_contents($fileName,$content);
    }

    private static function _checkDir($fileName){
        $info = pathinfo($fileName);
        if(!is_dir($info['dirname'])){
            mkdir($info['dirname'],0777,true);
        }
    }

    public static function getContent($name){
        self::checkInit();
        $fileName = self::$shareConfig['file_path'].$name;
        if(!file_exists($fileName)){
            return '';
        }
        return file_get_contents($fileName);
    }

    private static function checkInit(){
        if(empty(self::$shareConfig)){
            throw new \Exception(__CLASS__."未初始化");
        }
    }

}