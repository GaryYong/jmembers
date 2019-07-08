<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2018/12/1
 * Time: 上午10:48
 */

namespace console\job;
use console\factory\FDB;
use console\lib\functions;

class Overview extends Job{
    public function run(){
        echo __CLASS__."执行\r\n";
        $date = $this->getItemCfg('date',date('Y-m-d'));
        $day = $this->getItemCfg('day',0);
        $startTime = strtotime("{$day} day".$date);
        $endTime = $startTime+(24*3600);
        $channelList = functions::getChannelList();
        list($mysql,$table) = FDB::gm('report_data');
        $sysTime = time();
        $channelIds = implode(',',array_keys($channelList));
        $channelList[0] = 0;
        foreach ($channelList as $channelId =>$channelName){
            $tmpChannelId = $channelId;
            $reportData = [
                'log_time' => $startTime,
                'human_date' => date('Y-m-d',$startTime),
                'log_type' => 1,
                'channel_id' => $channelId,
                'game_id'   => 1,
                'log_data'  => '',
                'ctime' => $sysTime
            ];
            $logData = [];
            if($channelId == 0){
                $channelWhere = "publisher in({$channelIds})";
                $channelWhere2 = "publish in({$channelIds})";
            }else{
                $channelWhere = "publisher={$channelId}";
                $channelWhere2 = "publish={$channelId}";
            }
            //登陆用户数，新增用户数
            $sql = "SELECT count(DISTINCT actorid) cnt,sum(if(firstlogin=1,1,0)) cnt2 FROM gamelog.login WHERE logintime >= {$startTime} AND logintime < {$endTime} AND {$channelWhere}";
            $ret = $mysql->query($sql)->get_row(true);
            $logData['login_cnt'] = isset($ret['cnt']) ? intval($ret['cnt']) : 0;
            $logData['login_new_cnt'] = isset($ret['cnt2']) ? intval($ret['cnt2']) : 0;

            //充值金额，充值用户数
            $sql = "SELECT sum(money) total_money,count(DISTINCT actorid) pay_unt FROM gamelog.pay WHERE `time` >= {$startTime} AND `time` < {$endTime} AND {$channelWhere2} AND result=1";
            $ret = $mysql->query($sql)->get_row(true);
            $logData['pay_money'] = isset($ret['total_money']) ? intval($ret['total_money']) : 0;
            $logData['pay_user_cnt'] = isset($ret['pay_unt']) ? intval($ret['pay_unt']) : 0;

            $channelId = $channelId ? $channelId : $channelIds;
            foreach ([1,3,7,30] as $day){
                $logData['stay'][$day] = 0;
                $stayTime = $startTime - ($day*24*3600);
                $stayCnt = functions::getStayData($mysql,$startTime,$channelId,$day);
                $staySql = "SELECT log_time,channel_id,log_data FROM {$table} WHERE log_time = {$stayTime} AND log_type = 1 AND channel_id={$tmpChannelId}";
                $stayData = $mysql->query($staySql)->get_row(true);
                $stayLogData = isset($stayData['log_data']) ? json_decode($stayData['log_data'],true) : [];
                $stayLogData = is_array($stayLogData) ? $stayLogData : [];
                $stayLogData['stay'][$day] = $stayCnt;
                $mysql->insertByPrimary(
                    [
                        'log_time' => $stayTime,
                        'game_id'  => 1,
                        'human_date' => date('Y-m-d',$stayTime),
                        'log_type' => 1,
                        'channel_id' => $tmpChannelId,
                        'log_data' => json_encode($stayLogData),
                        'ctime' => $sysTime
                    ], $table, ['log_time','game_id','log_type','channel_id']);
            }
            $reportData['log_data'] = json_encode($logData);
            $mysql->insertByPrimary($reportData, $table, ['log_time','game_id','log_type','channel_id']);
        }

    }
}