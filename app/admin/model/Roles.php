<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use app\common\model\Base;

class Roles extends Base{
    protected $pk = 'role_id';
    protected $field = array('role_id','role_name','role_status','add_time');
    public function getRoleMap(){
        $result = $this->field('role_id,role_name')->select();
        $roles = array();
        foreach ($result as $k=>$v){
            $v = $v->toArray();
            $roles[$v['role_id']] = $v['role_name'];
        }
        return $roles;
    }

    public function getValidRoles($exclude_id=false){
        if($exclude_id)$this->where('role_id','>',$exclude_id);
        return $this->where('role_status',0)->field('role_id,role_name')->select();
    }

}