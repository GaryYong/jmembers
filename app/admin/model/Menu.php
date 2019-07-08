<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use app\admin\model\Base;
use think\Session;

class Menu extends Base{
    protected $pk = 'id';

    public function getValidMenus(){
        $data = $this->where('display',0)->select();
        if($data){
            $data = $data->toArray();
        }
        return $data;
    }

    public function getShowMenus($game_id=0){
        $table = $this->db()->getTable();
        $find_in_set = '';
        $find_in_set2 = '';
        if($game_id){
            $find_in_set = "AND find_in_set('{$game_id}',game_ids)";
            $find_in_set2 = "AND find_in_set('{$game_id}',a.game_ids) AND find_in_set('{$game_id}',b.game_ids)";
        }
        $sql = "(SELECT * from {$table} WHERE parentid = 0 and display = 0 {$find_in_set} order by listorder asc,id asc) 
        UNION (select a.* from {$table} as a right join {$table} as b on a.parentid=b.id where b.parentid = 0 and b.display = 0 and a.display = 0 {$find_in_set2} order by b.listorder asc,b.id asc);";
        return $this->db()->query($sql)->toArray();
    }

    public function getPrivMenus(){
        $game_id = get_game_id();
        $use_session = Session::get('gm_admin');
        //var_dump($game_id,$use_session);
        if($use_session['role_id'] != 1 && empty($use_session['game_ids'][0])){
            $game_id = -1;
        }else if($use_session['role_id'] != 1 && !in_array($game_id,$use_session['game_ids'])){
            $game_id = -1;
        }
        $menus = $this->getShowMenus($game_id);
        $isAdmin = Session::get('gm_admin.role_id');
        $userPrivs = Session::get('user_privs');
        if(empty($userPrivs) && $isAdmin != 1){
            return false;
        }

        $validMenus = array();
        foreach ($menus as $k=>$v){
            $mca = strtolower($v['m'].$v['c'].$v['a']);
            if(!in_array($mca,$userPrivs) && $isAdmin != 1){
                continue;
            }

            if($v['parentid']){
                $validMenus[$v['parentid']]['children'][] = $v;
            }else{
                $validMenus[$v['id']] = $v;
            }
        }
        return $validMenus;
    }

    public function init_session_menu(){
        Session::delete('gm_menus');
        Session::set('gm_menus',$this->getPrivMenus());
    }

    public function getTableName(){
        return $this->db()->getTable('');
    }


    public function getParentMenu(){
        $id = 0;
        if(ENVIRONMENT == 'pre'){
            $id = 78;
        }
        return $this->where('id','<>',$id)->where('parentid',0)->where('display',0)
            ->order('listorder asc,id asc')->select();
    }


    public function getMenuById($id){
        $table = $this->db()->getTable('');
        //return $this->where(array('id'=>$id))->order(array('listorder'=>'asc','id'=>'asc'))->select()->getLastSql();
    }

    /**
     * 获取父菜单
     * @param $controller
     * @param $action
     * @return array
     */
    public function getParentsByControllerAndAction($controller,$action){
        $m = \think\Request::instance()->module();
        $menus = array();

        $field = 'id,parentid,name';
        $data = $this->where(array('m'=>$m,'c'=>$controller,'a'=>$action))->field($field)->order('id desc')->find()->toArray();
        if($data){
            array_unshift($menus,$data);
        }

        while ($data && $data['parentid'] > 0){
            $data = $this->where(array('id'=>$data['parentid']))->field($field)->find()->toArray();
            if($data){
                array_unshift($menus,$data);
            }else{
                $data = '';
            }
        }
        return $menus;

    }

    public function delAllChildren($id){
        $flag = false;
        $flag = $this->where(array('id'=>$id))->delete();
        if($flag){
            $childIds = $this->where(array('parentid'=>$id))->field('id')->select()->toArray();
            if(!$childIds){return false;}
            foreach ($childIds as $k=>$v){
                $flag = $this->delAllChildren($v['id']);
            }
        }

        return $flag;
    }

    public function getAllChildren($id){
        static $i = 0;
        $i++;
        $exists = $this->where(array('id'=>$id))->count();
        $ids = array();
        if($exists){
            $i == 1 && $ids[] = $id;
            $childIds = $this->where(array('parentid'=>$id))->field('id')->select()->toArray();
            foreach ($childIds as $k=>$v){
                $ids[] = $v['id'];
                $cdata = $this->getAllChildren($v['id']);
                if(!$cdata){continue;}
                $ids = array_merge($ids,$cdata);
            }
        }
        return $ids;
    }



    public function public_push_config(){

    }


}