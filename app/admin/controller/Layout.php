<?php
/**
 * 公共模版存放地址
 * author liuc
 * createTime 2017/11/12
 */

namespace app\admin\controller;
use app\common\controller\Admin;

class Layout extends Admin{
   
    public function __construct(){
        parent :: __construct();
    }
    
    public function header(){
        return $this -> fetch("layout/header");
    }
    
    public function footer(){
        return $this -> fetch("layout/footer");
    }
}