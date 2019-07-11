<?php
namespace think;
use think\Model;

/**
 * 使用db模式
 * use think\Db;
 * return Db::query("select * from tp_user");
 * return D("User") -> select();
 * $user = new User();
 * return $user -> select();
 */
class BaseModel extends Model{
    /**
     * 根据条件查询数据
     * @params $where => array 查询条件
     * @params $order => array 排序
     * @params $group => string 分组字段
     * @return array
     */
    public function getWhereData( $where = array() , $order = array() , $field = array() , $group = ""){
        if(empty($field) || !is_array($field)) $field = "*";
        if(!empty($where) && is_array($where)) $this -> where($where);
        if(!empty($order) && is_array($order)) $this -> order($order);
        if(!empty($group) && is_string($group)) $this -> group($group);
        $obj = $this -> field($field) -> select();
        $data = array();
        if(!empty($obj)){
            foreach($obj as $key => $val){
                $data[] = $val -> data;
            }
        }
        return $data;
    }

    /**
     * 根据条件查询一条数据
     * @params $where => array 查询条件
     * @params $order => array 排序
     * @params $group => string 分组字段
     * @return array
     */
    public function getFirstData($where = array() , $order = array() , $field = array() , $group = ""){
        if(empty($field) || !is_array($field)) $field = "*";
        if(!empty($where) && is_array($where)) $this -> where($where);
        if(!empty($order) && is_array($order)) $this -> order($order);
        if(!empty($group) && is_string($group)) $this -> group($group);
        $obj = $this -> field($field) -> find();
        //echo $this -> getLastSql();
        if(!empty($obj)){
            return $obj -> data;
        }
        return array();
    }

    /**
     * 查询所有数据
     * @params $order   => array() $order = array("id" => "desc");
     * @params $group   => string  group('user_id') Group方法的参数只支持字符串
     * return array
     */
    public function getAllData($order = array() , $field = array() ,  $group = ""){
        if(empty($field) || !is_array($field)) $field = "*";
        $data = array();
        if(!empty($order) && is_array($order)) $this -> order($order);
        if(!empty($group) && is_string($group)) $this -> group($group);
        $obj = $this -> field($field) -> select();
        if(!empty($obj)){
            foreach($obj as $key => $val){
                $data[] = $val -> data;
            }
        }
        return $data;
    }

    /**
     * 根据ID查询数据
     * @params $id => string 查询主键ID
     * @return array
     */
    public function getById($id , $field = array()){
        if(empty($field) || !is_array($field)) $field = "*";
        if(!empty($id) && is_numeric($id)){
            $data = $this -> where(array("id" => $id)) -> field($field) -> find();
            return $data -> data;
        }
        return array();
    }

    /**
     * 分页获取数据
     * @params $page => number 当前页数
     * @params $limit => number 每页显示条数
     * @params $where => array() 查询条件
     * @params $order => array() 排序
     * @params $group => string 分组 分组的字段
     * @return array
     */
    public function getPageList($page = 1 , $limit = 20 , $where = array() , $order = array() , $field = array() , $group = ""){
        $dataList = array();
        if($page < 1 || !is_numeric($page)){
            $page = 1;
        }
        $start = ($page-1)*$limit;
        if(empty($field) || !is_array($field)) $field = "*";
        if(!empty($where) && is_array($where)) $this -> where($where);
        if(!empty($order) && is_array($order)) $this -> order($order);
        if(!empty($group) && is_string($group)) $this -> group($group);
        $data = $this -> limit($start , $limit) -> field($field) -> select();
        if(!empty($data)){
            foreach($data as $key => $val){
                $dataList[] = $val -> data;
            }
        }
        if(!empty($where) && is_array($where)) $this -> where($where);
        $count = $this -> count();
        return array("count" => $count , "dataList" => $dataList);
    }

    /**
     * 条件查询返回总条数
     * @param array $where 查询条件
     * @return int
     */
    public function countNum($where = array()){
        if(!empty($where) && is_array($where)) $this -> where($where);
        $data = $this -> count();
        return $data;
    }

    /**
     * 根据条件删除数据 返回删除的条数
     * @params $params => array 删除条件
     * @return int
     */
    public function deleteData($where = array()){
        if(!is_array($where)) return false;
        $data = $this -> where($where) -> delete();
        return $data;
    }

    /**
     * 根据id删除数据 返回删除的条数
     * @params $id => number 删除条件的Id
     * @return int
     */
    public function deleteById($id){
        if(!empty($id) && is_numeric($id)) $where['id'] = $id;
        $data = $this -> where($where) -> delete();
        return $data;
    }

    /**
     * 新增数据  成功返回插入ID 失败返回false；
     * @params $params => array 插入数据
     * @return int
     */
    public function insertData($params = array()){
        if(!is_array($params)) return false;
        $this -> data($params) -> save();
        return $this -> id;
    }

    /**
     * 修改数据 修改成功返回 1 失败 返回0
     * @params $params => array 修改的数据
     * @params $where  => array 查询条件
     * @return int
     */
    public function updateData($params , $where){
        if(!is_array($params)) return false;
        if(!is_array($where)) return false;
        return $this -> where($where) -> update($params);
    }

    /**
     * 根据ID修改数据
     * @params $params => array 修改的数据
     * @params $id  => number 查询id
     * @return int 1 成功 0 失败
     */
    public function updateById($params = array() , $id = ""){
        if(!is_array($params)) return false;
        if(!empty($id) && is_numeric($id)) $where['id'] = $id;
        
        $this -> where($where) -> update($params);
        return $this -> getLastSql();
    }
}