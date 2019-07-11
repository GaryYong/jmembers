<?php
namespace app\admin\controller;
use app\common\controller\Admin;

class Role extends Admin
{
    private $model = null;
    public function __construct()
    {
        parent::__construct();
        $this->model = model('Roles');
    }

    public function index()
    {
        $roleList = $this->model->select();
        $this->assign('roleList',$roleList);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $_POST['add_time'] = time();
            $result = $this->model->validate(
                [
                    'role_name' => 'require|unique:roles',
                    'role_status' => 'require'
                ],
                [
                    'role_name.require' => '请输入角色名',
                    'role_name.unique' => '角色名已存在',
                    'role_status.require' => '请选择角色状态'
                ]
            )->data($_POST)->save();

            if(false === $result){
                return json(['status'=>'n','msg'=>$this->model->getError()]);
            }
            return json(['status'=>'y','msg'=>'ok']);

        }
        return $this->fetch();
    }

    public function edit(){
        if($this->request->isPost()){

            $role_id = input('role_id',1,'intval');
            if(empty($role_id)){
                return json(['status'=>'n','msg'=>'修改失败']);exit;
            }
            if($role_id == 1 || empty($role_id)){
                return json(['status'=>'n','msg'=>'超级管理不允许修改']);exit;
            }
            $role_name = input('role_name');
            $count = $this->model->where('role_id','neq',$role_id)->where('role_name',$role_name)->count();
            if($count){
                return json(['status'=>'n','msg'=>'角色名已存在']);exit;
            }

            unset($_POST['role_id']);
            $result = $this->model->validate(
                [
                    'role_name' => 'require',
                    'role_status' => 'require'
                ],
                [
                    'role_name.require' => '请输入角色名',
                    'role_name.checkExistsName' => '角色名已存在',
                    //'role_name.unique' => '角色名已存在',
                    'role_status.require' => '请选择角色状态'
                ]
            )->save($_POST,['role_id'=>$role_id]);

            if(false === $result){
                return json(['status'=>'n','msg'=>$this->model->getError()]);
            }
            return json(['status'=>'y','msg'=>'ok']);
        }

        $role_id = $this->request->get('role_id');
        $result = $this->model->where('role_id',$role_id)->find()->toArray();
        $this->assign('result',$result);
        return $this->fetch();
    }


    public function priv(){
        $MenuModel = model('Menu');
        $RolePrivModel = model('RolePriv');

        if($this->request->isPost()){
            $menu_ids = isset($_POST['menuid']) ? $_POST['menuid'] : '';
            $role_id = $this->request->post('role_id');
            if (is_array($menu_ids) && count($menu_ids) > 0) {
                $RolePrivModel::where(array('role_id'=>$role_id))->delete(true);
                $menuinfo = $MenuModel->getValidMenus();
                foreach ($menuinfo as $_v) $menu_info[$_v['id']] = $_v;
                $saveData = array();
                foreach($menu_ids as $menuid){
                    $info = array();
                    $info['m'] = $menu_info[$menuid]['m'];
                    $info['c'] = $menu_info[$menuid]['c'];
                    $info['a'] = $menu_info[$menuid]['a'];
                    $info['data'] = $menu_info[$menuid]['data'];
                    $info['role_id'] = $role_id;
                    $saveData[] = $info;

                }

                $RolePrivModel->saveAll($saveData);
            } else {
                $RolePrivModel::where(array('role_id'=>$role_id))->delete(true);
            }

            return json(['status'=>'y','msg'=>'操作成功！']);
        }


        $role_id = $this->request->get('role_id');
        if(empty($role_id)){
            $this->error('权限设置失败！');
        }


        $menu = new \tree\tree();
        $menu->icon = array('│ ','├─ ','└─ ');
        $menu->nbsp = '&nbsp;&nbsp;&nbsp;';

        $result = $MenuModel->getValidMenus();
        $priv_data = $RolePrivModel->getCMAByRoleId($role_id);
        foreach ($result as $n=>$t) {
            $menu_str = $t['m'].$t['c'].$t['a'];
            $menu_str = strtolower($menu_str);
            $result[$n]['cname'] = $t['name'];
            $result[$n]['checked'] = (in_array($menu_str,(array)$priv_data))? ' checked' : '';
            $result[$n]['level'] = $this->get_level($t['id'],$result);
            $result[$n]['parentid_node'] = ($t['parentid'])? ' class="child-of-node-'.$t['parentid'].'"' : '';
        }

        $str  = "<tr id='node-\$id' \$parentid_node>
							<td style='padding-left:30px;'>\$spacer<input type='checkbox' name='menuid[]' value='\$id' level='\$level' \$checked onclick='checknode(this);'> \$cname</td>
						</tr>";

        $menu->init($result);
        $categorys = $menu->get_tree(0, $str);
        $this->assign('categorys',$categorys);
        $this->assign('role_id',$role_id);
        return $this->fetch();
    }

    public function ajax_delete(){
        if($this->request->isPost()){
            $role_id = $this->request->post('role_id',0,'intval');

            if(empty($role_id)){
                return json(['status'=>'n','msg'=>'删除失败！']);
            }

            if($role_id == 1){
                return json(['status'=>'n','msg'=>'超级管理不允许删除']);
            }


            if($role_id == 1){
                return json(['status'=>'n','msg'=>'"系统管理员"角色不允许删除！']);
            }


            $RolePrivModel = model('RolePriv');
            $flag = false;
            $flag = $this->model->where(array('role_id'=>$role_id))->delete();

            if($flag){
                $RolePrivModel->where(array('role_id'=>$role_id))->delete();
                return json(['status'=>'y','msg'=>'删除成功！']);
            }

            return json(['status'=>'n','msg'=>'删除失败！']);

        }
    }



    /**
     * 获取菜单深度
     * @param $id
     * @param $array
     * @param $i
     */
    private function get_level($id,$array=array(),$i=0) {
        if($i >=5) return $i;
        foreach($array as $n=>$value){
            if($value['id'] == $id)
            {
                if($value['parentid']== '0') return $i;
                $i++;
                return $this->get_level($value['parentid'],$array,$i);
            }
        }
    }

    
}
