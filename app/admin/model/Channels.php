<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use app\admin\model\Base;

class Channels extends Base{
    protected $pk = 'id';
    protected $field = array('game_id','channel_id','channel_name','channel_alias_name','pack_name','ctime');

    public function checkExists($gameId,$channelId,$id=false){
        if($id){
            $this->where('id','NEQ',$id);
        }
        return $this->where('game_id',$gameId)->where('channel_id',$channelId)->count();
    }

    public function getMapping($game_id=false){
        if($game_id)$this->where('game_id',$game_id);
        $result = $this->field('game_id,channel_id,channel_name')->select()->toArray();
        $list = [];
        foreach ($result as $rv){
            $list[$rv['game_id']][$rv['channel_id']] = $rv['channel_name'];
        }
        return $list;
    }

}