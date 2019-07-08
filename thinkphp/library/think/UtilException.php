<?php
/**
 * Created by PhpStorm.
 * User: JLB9858
 * Date: 2016/10/26
 * Time: 9:45
 */

namespace think;

use think\Config;

class UtilException extends Exception{

    private $errorCode;

    public function __construct($code , $message = null)
    {
        Config::load(CONF_PATH."errorcode.php" , "errorcode");
        $this -> errorCode = Config::get("errorcode");

        if(empty($code)){
            $code = -1;
            $message = "throw exception code";
        }

        if(empty($message)){
            $message = $this -> errorCode[$code];
        }

        parent::__construct($message, $code);
    }



}