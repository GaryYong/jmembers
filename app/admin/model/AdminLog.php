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

class AdminLog extends Model{
    protected $connection = 'database.db_admin';
    protected $resultSetType = 'collection';
    protected $table = 'gm_admin_log';

    const DEL_USER_RED_PACK_TYPE = 1;//删除用户未领取红包类型

    public function saveLog($reason,$opt_type){
        $data['game_id'] = get_game_id();
        $data['admin_id'] = Session::get('gm_admin.id');
        $data['admin_name'] = Session::get('gm_admin.user_name');
        $data['opt_type'] = $opt_type;
        $data['reason'] = $reason;
        return $this->data($data)->save();
    }
}