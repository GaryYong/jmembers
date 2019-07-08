<?php
/**
 * api外发url访问接口
 */
namespace app\api\controller;
use app\api\controller\BaseController;
use think\UtilException;
use Api\State;

class Users extends BaseController {

    public function checkToken(){
        $requestData = $this->requestData(['op'=>'require','token'=>'require']);
        $op = $requestData['op'];
        $token = $requestData['token'];
        $ret = model('admin/Users')->checkToken($op,$token);
        if($ret){
            output(State::SUCCESS_CODE,'ok',[]);
        }else{
            throw new UtilException(State::TOKEN_ERROR,"验证失败");
        }
    }

}