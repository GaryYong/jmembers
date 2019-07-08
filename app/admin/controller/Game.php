<?php
namespace app\admin\controller;
use Api\GameInfo;
use app\admin\controller\Admin;
use think\Config;
use think\Request;
use think\Db;

class Game extends Admin{

    private $modifyUserTypes = [  0 => '金币',1 => '充值点数',2 => '摇钱树点数',3=>'喇叭'];
    private $banTypes = [ 2 => '禁言',3 => '封号',4 => '解除禁言',5 => '解除封号'];
    private $mailTypes = [1=>'普通邮件',2=>'系统公告',3=>'活动邮件'];

    private $moneyChangeTypes = [1=>'下注/赢取',2=>'奖励',3=>'赠送',4=>'其他'];

    private $rankTypes = [
        1 => '金币榜',
        2 => 'vip榜',
        3 => '昨日充值榜',
        101 => '森林舞会今日豪胜榜',
        102 => '红黑（龙凤）斗今日豪胜榜',
        103 => '天地玄黄今日豪胜榜',
        104 => '时时彩豪胜榜',
        201 => '森林舞会昨日豪胜榜',
        202 => '红黑（龙凤）斗昨日豪胜榜',
        203 => '天地玄黄昨日豪胜榜'
    ];
    private $searchMailTypes = array(
        1 => '收件人id',
        2 => '发件人id',
        3 => '邮件标题'
    );
    private $gameSubTypes = [0=>'所有游戏',1=>'森林舞会',2=>'龙凤斗',3=>'天地玄黄',100=>'斗地主',110=>'欢乐三张',1000=>'时时乐'];
    //1 森林舞会 2 龙凤斗 3 天地玄黄 1000 时时乐 有暗池
    private $hidePoolTbType = [1=>'森林舞会',2=>'龙凤斗',3=>'天地玄黄',1000=>'时时乐'];
    const HIDE_POOL_TYPE = 1;

    public function game_info()
    {
        $info = false;
        try{
            $GmApi = new GameInfo();
            $info = $GmApi->getInfo();
        }catch (\Exception $ex){
            $info = $ex->getMessage();
        }
        $this->assign('info',$info);
        $this->assign('gameSubTypes',$this->gameSubTypes);
        return $this->fetch('game_info');
    }


    public function mail()
    {
        $data = false;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $page = $page ? $page : 1;
        $pages = '';
        if(isset($_GET['do_submit'])){
            $search_type = input('search_type',0,'intval');
            $key = input('key');
            try{
                $GmApi = new GameInfo();
                $data = $GmApi->mail($search_type,$key,$page);
                $pages = pages($data['mailCount'],$page);
            }catch (\Exception $ex){
                $data = $ex->getMessage();
            }

        }
        $this->assign('data',$data);
        $this->assign('page',$pages);

        $this->assign('searchMailTypes',$this->searchMailTypes);
        $this->assign('mailTypes',$this->mailTypes);
        return $this->fetch('mail');
    }


    public function hide_pool()
    {
        $logModel = model('OpeLog');
        $where = ['game_id'=>get_game_id(),'mtype'=>self::HIDE_POOL_TYPE];
        $data = $logModel->where($where)->order('id desc')->paginate(20);
        $page = $data->render();
        $this->assign('pages', $page);
        $this->assign('data',$data);

        $this->assign('hide_pool_type',$this->hidePoolTbType);
        return $this->fetch('hide_pool');
    }


    public function public_modify_hide_pool(){

        if(Request::instance()->isPost()){
            $tb_type = input('tb_type',0,'intval');

            if(empty($tb_type)){
                return json(['status'=>'n','msg'=>'操作失败']);
            }
            $value = input('value','','trim');
            if(empty($value)){
                return json(['status'=>'n','msg'=>'操作失败']);
            }

            $ret = false;
            try{
                $GmApi = new GameInfo();
                $ret = $GmApi->modify_hide_pool($tb_type,$value);
                if($ret){
                    //记录日志
                    $logData = array(
                        'game_id'       => get_game_id(),
                        'mtype'         => self::HIDE_POOL_TYPE,
                        'content'       => "场次：【{$this->hidePoolTbType[$tb_type]}】,修改暗池，数值为：{$value}",
                    );
                    model('OpeLog')->saveLog($logData);
                }
            }catch (\Exception $ex){
                return json(['status'=>'n','msg'=>$ex->getMessage()]);
            }

            if($ret){
                return json(['status'=>'y','msg'=>'操作成功']);
            }else{
                return json(['status'=>'n','msg'=>'操作失败']);
            }
        }
        $this->assign('hide_pool_tb_type',$this->hidePoolTbType);
        return $this->fetch('modify_hide_pool');
    }

    public function ajax_del_mail(){
        if($this->request->isPost()){
            try{
                $mailId = input('post.id',0,'intval');
                $GmApi = new GameInfo();
                $GmApi->del_mail($mailId);
                return json(['status'=>'y','msg'=>'删除成功']);
            }catch (\Exception $ex){
                return json(['status'=>'n','msg'=>$ex->getMessage()]);
            }

        }
        return json(['status'=>'n','msg'=>'删除失败']);
    }

    public function channel_data()
    {
        $channelModel = model('Channels');
        $channelList = $channelModel->getMapping(get_game_id());
        $g_c_id = input('g_c_id');
        if($g_c_id){
            list($game_id,$channel_id) = explode('_',$g_c_id);
        }else{
            $game_id = current(array_keys($channelList));
            $channel_id = current(array_keys($channelList[$game_id]));
        }

        $this->assign('g_id',$game_id);
        $this->assign('c_id',$channel_id);

        $this->assign('channel_list',$channelList);
        $where = "game_id ={$game_id} AND channel_id={$channel_id}";

        if(!empty($_GET['start_date'])){
            $startTime = strtotime($_GET['start_date']);
            $where .= " AND log_time >= {$startTime}";
        }

        if(!empty($_GET['end_date'])){
            $endTime = strtotime($_GET['end_date']);
            $where .= " AND log_time <= {$endTime}";
        }

        $reportDataModel = model('ReportData');
        $list = $reportDataModel->where($where)->order('log_time DESC')->paginate(30);
        $page = $list->render();
        $this->assign('list',$list);
        $this->assign('page',$page);

        return $this->fetch('channel_data');
    }
}
