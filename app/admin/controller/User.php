<?php
namespace app\admin\controller;
use app\admin\controller\Admin;
use think\Db;

class User extends Admin
{
    private $UserModel = null;
    public function __construct()
    {
        parent::__construct();
        $this->UserModel = model('Users');
    }

    public function index()
    {
        $where = "1=1";
        $userData = $this->UserModel->where($where)->paginate(20);
        $page = $userData->render();
        $this->assign('page', $page);

        $this->assign('userData',$userData);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $_POST['game_ids'] = isset($_POST['game_ids']) ? $_POST['game_ids'] : [];
            $_POST['game_ids'] = implode(',',$_POST['game_ids']);
            $result = $this->UserModel->addUser($_POST);
            if(empty($result)){
                return json(['status'=>'n','msg'=>$this->UserModel->getError()]);
            }

            return json(['status'=>'y','msg'=>'ok']);
        }
        $roleModel = model('Roles');
        $roleData = $roleModel->getValidRoles(1);
        $this->assign('roleData',$roleData);
        return $this->fetch();
    }

    public function edit(){

        if($this->request->isPost()){
            $id = $this->request->post('id',0,'intval');
            $userName = input('user_name');
            //unset($_POST['id']);
            $count = $this->UserModel->where('id','neq',$id)->where('user_name',$userName)->count();
            if($count){
                return json(['status'=>'n','msg'=>'用户名已存在']);exit;
            }

            $_POST['game_ids'] = isset($_POST['game_ids']) ? $_POST['game_ids'] : [];
            $_POST['game_ids'] = implode(',',$_POST['game_ids']);
            $result = $this->UserModel->updateUser($_POST,$id);
            if($result === false){
                return json(['status'=>'n','msg'=>$this->UserModel->getError()]);
            }

            return json(['status'=>'y','msg'=>'ok']);
        }


        $id = $this->request->get('id',0,'intval');
        if(empty($id)){
            $this->error('操作错误');
        }

        $userData = $this->UserModel->where('id',$id)->find()->toArray();
        $userData['game_ids'] = (array)explode(',',$userData['game_ids']);
        $this->assign('userData',$userData);
        $RoleModel = model('Roles');
        $roleData = $RoleModel->getValidRoles();
        $this->assign('roleData',$roleData);
        return $this->fetch();
    }


    public function ajax_delete(){

        if($this->request->isPost()){
            $id = $this->request->post('id',0,'intval');

            if(empty($id)){
                return json(['status'=>'n','msg'=>'删除失败！']);
            }

            if($id == 1){
                return json(['status'=>'n','msg'=>'"系统管理员"不允许删除！']);
            }


            if($id == $this->getUserSession('id')){
                return json(['status'=>'n','msg'=>'不允许删除自己！']);
            }
            $flag = false;
            $flag = $this->UserModel->where(array('id'=>$id))->delete();
            if($flag){
                return json(['status'=>'y','msg'=>'删除成功！']);
            }else{
                return json(['status'=>'n','msg'=>'删除失败！']);
            }

        }
    }

}
