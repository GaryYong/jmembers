<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use think\Model;

class Base extends Model{
    protected $connection = 'database.db_admin';
    protected $resultSetType = 'collection';
}