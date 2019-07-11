<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use app\common\model\Base;

class RolePriv extends Base {
    public function getTableName(){
        return $this->db()->getTable('');
    }



    public function getCMAByRoleId($role_id){
        $result = $this->where(array('role_id'=>$role_id))->field('CONCAT(m,c,a) as priv')->select();

        if($result){
            $data = array();
            foreach ($result as $k=>$v){
                $_vdata = $v->data;
                $data[] = strtolower($_vdata['priv']);
            }
            return $data;
        }
        return false;
    }
}