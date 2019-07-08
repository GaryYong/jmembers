<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use think\Model;
use think\Session;

class OpeLog extends Model{
    protected $connection = 'database.db_admin';
    protected $resultSetType = 'collection';
    protected $table = 'gm_ope_log';

    public function saveLog($data){
        $data['game_id'] = get_game_id();
        $data['user_id'] = Session::get('gm_admin.id');
        $data['ctime'] = time();
        return $this->data($data)->save();
    }
}