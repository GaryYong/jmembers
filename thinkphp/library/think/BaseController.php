<?php
/**
 * api继承controller 验证数据
 * Created by PhpStorm.
 * User: JLB9858
 * Date: 2016/10/25
 * Time: 17:00
 */
namespace think;
use Api\State;
use think\Controller;
use think\Config;
use think\UtilException;//抛出错误

class BaseController extends Controller{

    private $errorCode;

    private $signKey;

    public function __construct()
    {
        $this ->signKey = Config::get("encryption_key");
        $this->decrypt();
    }

    //加密
    public function encryption($params = array()){
        if(empty($params) || !is_array($params)){
            $params = $_REQUEST;
        }
        $params['sign_key'] = $this -> signKey;
        ksort($params);
        $str = "";
        foreach($params as $key => $val){
            $str .= $key."%".$val."&";
        }
        return md5(rtrim($str , "&"));
    }

    //解密
    public function decrypt($params = array()){
        if(empty($params) || !is_array($params)){
            $params = $_REQUEST;
        }
        if(!isset($params['sign'])){
            throw new UtilException(State::REQUEST_PARAM_ERROR,'invalid param');
        }
        $params['sign_key'] = $this -> signKey;
        $sign = $params['sign'];
        unset($params['sign']);
        ksort($params);
        $str = "";
        foreach($params as $key => $val){
            $str .= $key."=".$val."&";
        }
        if(md5(rtrim($str , "&")) != $sign){
            throw new UtilException(State::SIGN_ERROR,'sign error');
        }
    }


}