<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/12/1
 * Time: 上午10:34
 */

namespace console\factory;

use library\wuti\shard\mysqlShard;

class FDB{
    public static function gameLog($tb){
        return mysqlShard::getDbAndTable('gamelog_share',0,$tb);
    }
    public static function gm($tb){
        return mysqlShard::getDbAndTable('gm_share',0,$tb);
    }
    public static function zzdr($tb){
        return mysqlShard::getDbAndTable('zzdr_share',0,$tb);
    }
}