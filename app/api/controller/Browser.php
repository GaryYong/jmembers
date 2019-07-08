<?php
/**
 * api外发url访问接口
 */
namespace app\api\controller;
use think\BaseController;
use think\image\Exception; //引用基类model

class Browser extends BaseController{

    public function index(){
        try{
            $this -> decrypt();
        }catch (Exception $e){

        }

    }
}