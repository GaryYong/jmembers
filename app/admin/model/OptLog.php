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
use util\utilConfig;

class OptLog extends Base{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
    protected $updateTime = false;
    const MODIFY_USER_LOG_TYPE = 0;
    const BAN_USER_LOG_TYPE = 1;


    public function saveModifyUserLog($logData){
        return $this->_saveLog($logData,self::BAN_USER_LOG_TYPE);
    }

    public function saveBanUserLog($logData){
        return $this->_saveLog($logData,self::BAN_USER_LOG_TYPE);
    }

    private function _saveLog($logData,$logType){
        $logData = is_array($logData) ? serialize($logData) : $logData;
        $data = array(
            'log_type' => $logType,
            'content'  => $logData,
            'user_id' => Session::get('gm_admin.id')
        );
        return $this->data($data)->save();
    }
}