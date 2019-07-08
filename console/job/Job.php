<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/12/1
 * Time: 上午10:48
 */
namespace console\job;
abstract class Job{
    protected $config = null;
    public static $channelList = [];
    public function __construct($config)
    {
        $this->config = $config;
        $run = true;
        $fileName = $config['call'].$config['idx'];
        foreach ($config['crontab'] as $k=>$val){
            if($val == '*'){continue;}
            $interval = false;
            if(strpos($val,'/') !== false){
                $interval = true;
                $val = intval(substr($val,2));
            }
            switch ($k){
                case 0://minute
                    $minute = date('i');
                    if(!$interval && $minute != $val){
                        $run = false;
                        break 2;
                    }else if($interval && !$this->checkLock($fileName,$val*60)){
                        $run = false;
                        break 2;
                    }
                    break;
                case 1://hour
                    $hour = date('H');
                    if(!$interval && $hour != $val){
                        $run = false;
                        break 2;
                    }else if($interval && !$this->checkLock($fileName,$val*3600)){
                        $run = false;
                        break 2;
                    }
                    break;
                case 2://day
                    $day = date('d');
                    if(!$interval && $day != $val){
                        $run = false;
                        break 2;
                    }else if($interval && !$this->checkLock($fileName,$day*24*3600)){
                        $run = false;
                        break 2;
                    }
                    break;
                case 3://month
                    $month = date('m');
                    if(!$interval && $month != $val){
                        $run = false;
                        break 2;
                    }else if($interval && !$this->checkLock($fileName,$month*30*24*3600)){
                        $run = false;
                        break 2;
                    }
                    break;
                case 4://week
                    $week = date('N');
                    if(!$interval && $week != $val){
                        $run = false;
                        break 2;
                    }else if($interval && !$this->checkLock($fileName,$week*7*24*3600)){
                        $run = false;
                        break 2;
                    }
                    break;
            }
        }

        if($run){
            $this->setLock($fileName);
            $this->run();
        }
    }

    public function getItemCfg($name,$default=false){
        return isset($this->config['params'][$name]) ? $this->config['params'][$name] : $default;
    }

    public function checkLock($fileName,$expireTime){
        $filePath = LOG_PATH.$fileName.".log";
        if(!$expireTime || !file_exists($filePath)){
            return true;
        }
        $fileContent = file_get_contents($filePath);
        $fileContent = $fileContent ? strtotime($fileContent) : 0;
        $nowTime = time();
        if($nowTime-$fileContent > $expireTime){
            return true;
        }
        return false;

    }
    public function setLock($fileName){
        $filePath = LOG_PATH.$fileName.".log";
        file_put_contents($filePath,date('Y-m-d H:i:s'));
    }

    abstract public function run();
}