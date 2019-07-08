<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use think\Model;

class Present extends Model{
    protected $connection = 'database.db_gamelog';
    protected $resultSetType = 'collection';
    protected $table = 'present';
    public function __construct()
    {
        $this->connection = getSiteDb();
    }

    public function getList(){
        return $this->paginate(10);
    }
}