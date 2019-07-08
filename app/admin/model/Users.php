<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use app\admin\model\Base;
use think\Config;
use think\Session;


class Users extends Base{

    protected $field = ['id','user_name','user_passwd','role_id',
        'user_nickname','user_status','token','last_login_time','last_login_ip','add_time','game_ids'];

    protected $pk = 'id';
    protected $createTime = 'add_time';
    protected $autoWriteTimestamp = true;
    protected $updateTime = null;

    const USER_ENABLE_STATUS = 0;
    const USER_DISABLE_STATUS = 1;


    /**
     * 通过用户ID获取角色ID
     * @param $id 用户id
     * @return mixed int OR false
     */
    public function getRoleIdById($id){
        $User = self::get($id);
        return $User->role_id;
    }

    public function updateUser($data,$id){
        if(empty($data['user_passwd'])){
            unset($data['user_passwd']);
        }else{
            $passwdSecret  = Config::get('user_passwd_secret');
            $data['user_passwd'] = md5($passwdSecret.$data['user_passwd']);
        }
        $flag = $this->validate([
            'user_name'   =>  'require|min:3',
            'role_id'    => 'require|number'
        ],[
            'user_name.require' => '请输入用户名！',
            'user_name.unique' => '输入的用户名已存在！',
            'user_name.min' => '用户名长度必须大于3个字符！',
            'role_id.require' => '请选择角色！',
            'role_id.number' => '请选择角色！'
        ])->save($data,['id'=>$id]);
        return $flag;
    }

    public function addUser($data){
        $passwdSecret  = Config::get('user_passwd_secret');
        $data['user_passwd'] = md5($passwdSecret.$data['user_passwd']);
       return $this->validate([
           'user_name'   =>  'require|unique:users|min:3',
           'user_passwd' =>  'require|min:5',
           'role_id'    => 'require|number'
       ],[
           'user_name.require' => '请输用户名！',
           'user_name.unique' => '抱歉，用户名已存在！',
           'user_name.min' => '用户名长度必须大于3个字符！',
           'user_passwd.require' => '请输入密码！',
           'user_passwd.min' => '密码长度必须大于5个字符！',
           'role_id.require' => '请选择角色！',
           'role_id.number' => '请选择角色！',
       ])->data($data)->save();
    }


    /**
     * 检测登陆
     * @param $user_name 用户名
     * @param $user_passwd 密码
     * @return bool|int array--成功，1--失败，2--登陆被禁止
     */
    public function checkLogin($user_name,$user_passwd,$role_id=false){
        if(empty($user_name) || empty($user_passwd))return false;
        $passwdSecret  = Config::get('user_passwd_secret');
        $user_passwd = md5($passwdSecret.$user_passwd);
        $map = array('user_name'=>$user_name,'user_passwd'=>$user_passwd);
        if($role_id)$map['role_id'] = $role_id;
        $data = $this->where($map)->find()->toArray();
        if($data && $data['user_name'] == $user_name && $data['user_passwd'] == $user_passwd){
            //被禁止登陆
            if($data['user_status'] == self::USER_DISABLE_STATUS){
                return 2;
            }
            $token = md5('gm_'.time());

            $data['token'] = $token;
            $Request = \think\Request::instance();
            $this->save(array('token'=>$token,'last_login_time'=>time(),'last_login_ip'=>$Request->ip()),['id'=>$data['id']]);

            $roleInfo = model('Roles')->where('role_id',$data['role_id'])->find()->toArray();
            $data['role_name'] = $roleInfo['role_name'];
            $data['game_ids'] = (array)explode(',',$data['game_ids']);
            unset($data['user_passwd']);
            if($data['role_id'] == 1){
                $data['game_id'] = 1;
            }else{
                $data['game_id'] = empty($data['game_ids'][0]) ? '' : current(array_keys($data['game_ids']));
            }
            Session::set('gm_admin',$data);
            $RolePrivModel = model('RolePriv');
            $rolePrivData = $RolePrivModel->getCMAByRoleId($data['role_id']);
            Session::set('user_privs',$rolePrivData);

            //获取菜单
            model('Menu')->init_session_menu();
            return $data;
        }else{
            return 1;
        }
    }


    public function setGameSess($gameId){
        $data = Session::get('gm_admin');
        $data['game_id'] = $gameId;
        Session::set('gm_admin',$data);
        return true;
    }

    public function checkToken($user_name,$token){
        $userInfo = $this->where('user_name',$user_name)->where('token',$token)->field('user_name,token,last_login_time')->find()->toArray();
        if($userInfo && $userInfo['user_name'] == $user_name && $userInfo['token'] == $token){
            return true;
        }
        return false;
    }

}