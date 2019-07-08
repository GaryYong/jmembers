<?php
namespace app\admin\controller;
use Api\GmUser;
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
        header("Content-type:text/html;charset=utf8;");
        $this->request = Request::instance();
        $this->isLogin();
        $this->assign('web_title',Config::get('web_title'));
        $this->assign("position" , Config::get("teacher_position"));
        $this->_checkPriv();

        $userSession = $this->getUserSession();

        $this->assign('userSession',$userSession);
        $this->assign('menus',Session::get('gm_menus'));
        $extConfig = config('ext_config');

        $site = [];
        $gameIds = $extConfig['game_ids'];
        $this->assign('game_ids',$gameIds);

        if($userSession['role_id'] == 1){
            $user_game_ids = $gameIds;
            $gameId = $this->getUserSession('game_id');
            $gameName = isset($gameIds[$gameId]) ? $gameIds[$gameId] : '地主大赢家';
            $this->assign('site',['game_id'=>$gameId,'game_name'=>$gameName]);
        }else{
            if(empty($userSession['game_ids'])){
                $user_game_ids = [];
                $this->assign('site',[]);
            }else{
                foreach ($gameIds as $gid=>$gname){
                    if(!in_array($gid,$userSession['game_ids'])){
                        unset($gameIds[$gid]);
                    }
                }

                if(!empty($gameIds)){
                    $site = [
                        'game_id' => get_game_id(),
                        'game_name' => $gameIds[get_game_id()]
                    ];
                }
                $this->assign('site',$site);
                $user_game_ids = $gameIds;
            }
        }

        $this->assign('use_game_ids',$user_game_ids);
        $request = Request::instance();
        if($request->controller() != 'Index' && $request->action() != 'public_select_game'){
            Session::set('gm_http_referer',"{$request->controller()}/{$request->action()}");
        }

    }


    protected function isLogin(){
        if(!Session::has('gm_admin') && $this->request->action() != 'login'){
            $this->redirect('Index/login');
        }
    }

    protected function setUserSession($value){
        return Session::set('gm_admin',$value);
    }

    protected function getUserSession($key = ''){
        $userSession = Session::get('gm_admin');
        if($userSession){
            if($key)
                return isset($userSession[$key]) ? $userSession[$key] : false;
            else
                return $userSession;
        }
        return false;
    }

    public function setSession($name,$value){
        return Session::set($name,$value);
    }

    public function _empty(){

    }

    private function _checkPriv(){
        $module = $this->request->module();
        $controller = $this->request->controller();
        $action = $this->request->action();
        $mca = $module.$controller.$action;
        $mca = strtolower($mca);

        //超级管理员直接跳过权限设置
        if($this->getUserSession('role_id') == 1){
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
