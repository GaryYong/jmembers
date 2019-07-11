<?php
namespace app\admin\controller;

use app\common\controller\Admin;
use think\Session;

class Index extends Admin
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return $this->fetch();
    }

    public function welcome(){
        return $this->fetch();
    }


    public function login(){
        if(is_login()){
            $this->redirect('Index/index');
        }

        if($this->request->isPost()){
            $user_name = $this->request->post('user_name');
            $passwd = $this->request->post('passwd');
            $image_code = $this->request->post('image_code');

            if(empty($user_name)){
                return json(['status'=>'n','msg'=>'请输入用户名!','id'=>'user_name']);
            }

            if(empty($passwd)){
                return json(['status'=>'n','msg'=>'请输入密码!','id'=>'passwd']);
            }

            /*
            if(empty($image_code)){
                return json(['status'=>'n','msg'=>'请输入验证码!','id'=>'image_code']);
            }
            if(!captcha_check($image_code)){
                return json(['status'=>'n','msg'=>'验证码错误!','id'=>'image_code']);
            }*/


            $UserModel = model('Users');
            $login = $UserModel->checkLogin($user_name,$passwd);
            if(is_array($login)){
                unset($login['user_passwd']);
                return json(['status'=>'y','msg'=>'登陆成功！']);
            }else if($login == 1){
                return json(['status'=>'n','msg'=>'用户名或密码错误！']);
            }else if($login == 2){
                return json(['status'=>'n','msg'=>'抱歉，您已被禁止登陆！']);
            }else{
                return json(['status'=>'n','msg'=>'登陆失败,请稍后重试！']);
            }
        }
        return $this->fetch('new_login');
    }

    public function loginOut(){
        Session::clear();
        $this->redirect('Index/login');
    }
}
