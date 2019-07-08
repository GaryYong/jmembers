<?php
/**
 * api继承controller 验证数据
 * Created by PhpStorm.
 * User: JLB9858
 * Date: 2016/10/25
 * Time: 17:00
 */
namespace app\api\controller;
use Api\Request;
use think\Config;
use think\Controller;
use think\UtilException;
use Api\State;

class BaseController extends Controller{
    public function requestData($validParam=array()){
        $data = $_REQUEST;
        foreach ($validParam as $key=>$param){
            $param = explode('|',$param);
            foreach ($param as $pv){
                switch ($pv){
                    case 'require':
                        if(!isset($data[$key])){
                            throw new UtilException(State::REQUEST_PARAM_ERROR,'invalid param');
                        }
                        break;
                    case 'number':
                        $data[$key] = isset($data[$key]) ? intval($data[$key]) : 0;
                        break;

                }
            }
        }
        return $data;
    }

}