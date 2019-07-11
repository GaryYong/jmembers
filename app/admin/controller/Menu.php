<?php
namespace app\admin\controller;
use app\common\controller\Admin;

class Menu extends Admin
{
    private $menuModel = null;
    public function __construct()
    {
        parent::__construct();
        $this->menuModel = model('Menu');
    }

    public function index()
    {
        $tree = new \tree\tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        //$result = $menuModel->order('listorder asc,id desc')->getData()->toArray();
        $result = $this->menuModel->order('listorder asc,id asc')->select()->toArray();
        $res_data = array();
        foreach ($result as $v){
            $v['manage'] = "<a href='javascript:layer_show(\"编辑菜单\",\"add.html?id=".$v['id']."\",700,380);'>添加</a> | <a href='javascript:layer_show(\"编辑菜单\",\"edit.html?id=".$v['id']."\",700,380);'>编辑</a> | <a href='javascript:del_menu(\"".$v['id']."\",\"是否删除[".$v['name']."]？\");'>删除</a>";
            $res_data[] = $v;
        }
        $str  = "<tr class='text-c'>
                <td>\$id</td>
                <td>\$spacer \$name</td>
                <td class='td-manage'>\$manage</td>
                </tr>";
        $tree->init($res_data);
        $categorys = $tree->get_tree(0, $str);
        $this->assign('categorys',$categorys);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $result = $this->menuModel->validate(
                [
                    'name' => 'require',
                    'm' => 'require',
                    'c' => 'require',
                    'a' => 'require',
                    'display' => 'require'
                ],
                [
                    'name.require' => '请输入菜单名',
                    'm.require' => '请输入模块名',
                    'c.require' => '请输入菜单文件名',
                    'a.require' => '请输入菜单方法名'
                ]
            )->save($_POST);

            if(false !== $result){
                return json(['status'=>'y','msg'=>'ok']);
            }
            return json(['status'=>'n','msg'=>$this->menuModel->getError()]);
            exit;
        }

        $id = $this->request->get('id');
        $id = intval($id);
        if(!empty($id)){
            $db_result = $this->menuModel->where('id',$id)->find()->toArray();
        }

        $tree = new \tree\tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        //$result = $menuModel->order('listorder asc,id desc')->getData()->toArray();
        $result = $this->menuModel->order('listorder asc,id asc')->select()->toArray();
        $array = array();
        foreach ($result as $k=>$v){
            $array[$v['id']] = $v;
        }
        unset($result);
        $str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $select_id = isset($db_result['id']) ? $db_result['id'] : '';
        $categorys = $tree->get_tree(0, $str,$select_id);
        $this->assign('menu_categorys',$categorys);
        return $this->fetch();
    }

    public function edit(){
        if($this->request->isPost()){

            $id = $this->request->post('id');
            $id = intval($id);
            if(empty($id)){
                return json(['status'=>'n','msg'=>'修改失败']);
            }
            $result = $this->menuModel->validate(
                [
                    'name' => 'require',
                    'm' => 'require',
                    'c' => 'require',
                    'a' => 'require',
                    'display' => 'require'
                ],
                [
                    'name.require' => '请输入菜单名',
                    'm.require' => '请输入模块名',
                    'c.require' => '请输入菜单文件名',
                    'a.require' => '请输入菜单方法名'
                ]
            )->save($_POST,['id'=>$id]);

            if(false === $result){
                return json(['status'=>'n','msg'=>$this->menuModel->getError()]);
            }

            return json(['status'=>'y','msg'=>'ok']);

            exit;
        }

        $id = $this->request->get('id');
        if(empty($id)){
            $this->error('参数错误！');
            exit;
        }

        $db_result = $this->menuModel->where('id',$id)->find()->toArray();
        $this->assign('result',$db_result);

        $tree = new \tree\tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';

        //$result = $menuModel->order('listorder asc,id desc')->getData()->toArray();
        $result = $this->menuModel->order('listorder asc,id asc')->select()->toArray();
        $array = array();
        foreach ($result as $k=>$v){
            $array[$v['id']] = $v;
        }
        unset($result);
        $str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
        $tree->init($array);
        $categorys = $tree->get_tree(0, $str,$db_result['parentid']);
        $this->assign('menu_categorys',$categorys);
        return $this->fetch();
    }

    public function ajax_delete(){
        if($this->request->isPost()){
            $id = $this->request->post('id','','intval');
            if(empty($id)){
                return json(['status'=>'n','msg'=>'删除失败']);
            }
            $this->menuModel->delAllChildren($id);
            return json(['status'=>'y','msg'=>'删除成功']);
        }
    }

}
