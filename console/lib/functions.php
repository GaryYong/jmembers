<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/12/2
 * Time: 上午9:18
 */
namespace console\lib;

use console\factory\FDB;
use library\wuti\database\dbMysqli;

class functions{
    public static function getChannelList($gameId=1){
        static $channelList = null;
        if($channelList){
            return $channelList;
        }
        list($mysql,$table) = FDB::gm('gm_channels');
        $result = $mysql->select('channel_id,channel_name',$table,['game_id'=>$gameId]);
        if($result){
            foreach ($result as $k=>$v){
                $channelList[$v['channel_id']] = $v['channel_name'];
            }

        }
        return $channelList;
    }

    public static function getStayData($db,$time,$channelId,$day){
        $yStartTime = strtotime("-{$day} day",$time);
        $yEndTime = $yStartTime+(24*3600);
        $starTime = $time;
        $endTime = $time+(24*3600);
        $channelWhere = strpos($channelId,',') !==false ? " publisher in({$channelId}) " : "publisher={$channelId}";
        $sql = "SELECT count(a.actorid) cnt FROM 
(SELECT DISTINCT actorid FROM gamelog.login WHERE {$channelWhere} AND firstlogin=1 AND logintime >= {$yStartTime} AND logintime < {$yEndTime}) a
INNER JOIN 
(SELECT DISTINCT actorid FROM gamelog.login WHERE {$channelWhere} AND logintime >= {$starTime} AND logintime < {$endTime}) b
ON a.actorid=b.actorid";
        $ret = $db->query($sql)->get_row(true);
        return isset($ret['cnt']) ? intval($ret['cnt']) : 0;
    }
}