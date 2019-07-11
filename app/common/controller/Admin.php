<?php
namespace app\common\controller;
use think\Controller;
use think\Config;
use think\Session;
use think\Request;
use util\utilPhp;

class Admin extends Controller
{
    protected $session_name = '';
    public $request = null;
    public $skip_priv = array(
        'adminindexlogin','adminindexindex','adminindexwelcome','adminindexloginout'
    );
    protected $channelSteps = array();
    public function __construct()
    {
        parent::__construct();
        $this->session_name = Config::get('app.session_name');
        header("Content-type:text/html;charset=utf8;");
        $this->request = Request::instance();
        $this->isLogin();
        $this->_checkPriv();
        $this->assign('userSession',get_admin_session());
        $this->assign('web_title',Config::get('app.site_title'));
        $this->assign('menus',Session::get('gm_menus'));
    }


    protected function isLogin(){
        if(!is_login() && $this->request->action() != 'login'){
            $this->redirect('Index/login');
        }
    }


    private function _checkPriv(){
        $module = $this->request->module();
        $controller = $this->request->controller();
        $action = $this->request->action();
        $mca = $module.$controller.$action;
        $mca = strtolower($mca);

        //超级管理员直接跳过权限设置
        if(get_admin_session('role_id') == 1){
            return true;
        }

        //方法名以public_开头的函数，将跳过权限,ajax_开头的待定
        if(strpos($action,'public_') !== false){
            return true;
        }

        //跳过不需要检测权限方法
        if(in_array($mca,$this->skip_priv)){
            return true;
        }

        $userPrivs = Session::get('user_privs');
        //$isPriv = false;
        if($userPrivs && in_array($mca,$userPrivs)){
           return true;
        }

        if($this->request->isAjax()){
            header('Content-type: application/json');
            echo json_encode(['status'=>'n','msg'=>'抱歉，无权限操作！']);
            exit;
        }

        abort(401,'抱歉，无权限操作！');
    }


    public function public_push_config(){
        $callback = input('callback');
        $ret = false;
        if($callback){
            $releaseData = call_user_func(array($this,$callback));
            $ret = utilPhp::outFileConfig($releaseData['file_name'],$releaseData['content']);
        }
        return $this->fetch('public/push_config');
    }

}
