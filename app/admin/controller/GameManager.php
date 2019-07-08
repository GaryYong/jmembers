<?php
namespace app\admin\controller;
use Api\GmUser;
use app\admin\controller\Admin;
use think\Db;
use think\Request;
use think\Session;
use util\utilPhp;

class GameManager extends Admin
{
    private $modifyUserTypes = [  0 => '金币',1 => 'vip点数',2 => '摇钱树点数',3=>'喇叭',4=>'累计充值',5=>'红包券'];
    private $banTypes = [ 2 => '禁言',3 => '封号',4 => '解除禁言',5 => '解除封号'];
    private $mailTypes = [1=>'普通邮件',2=>'系统公告',3=>'活动邮件'];

    public function index()
    {
        if(isset($_GET['do_submit'])){
            $search_type = input('search_type',0,'intval');
            $key = input('key');
            $userInfo = false;
            try{
                $GmApi = new GmUser();
                $userInfo = $GmApi->getUserInfo($search_type,$key);
                $userInfo = $userInfo ? $userInfo : '用户不存在';
            }catch (\Exception $ex){
                $userInfo = $ex->getMessage();
            }
            $this->assign('user_info',$userInfo);
        }
        $searchTypes = array(
            1   => 'id',
            2   => '昵称'
        );
        $gameChannelList = model('Channels')->getMapping();
        $this->assign('channel_list',isset($gameChannelList[get_game_id()]) ? $gameChannelList[get_game_id()] : []);
        $this->assign('searchTypes',$searchTypes);
        return $this->fetch('user_info');
    }

    public function rank()
    {
        if(isset($_GET['do_submit'])){
            $rankType = input('search_type',0,'intval');
            $key = input('key');
            $userInfo = false;
            try{
                $GmApi = new GmUser();
                $userInfo = $GmApi->rank($rankType,$key);
                $userInfo = $userInfo ? $userInfo : '用户不存在';
            }catch (\Exception $ex){
                $userInfo = $ex->getMessage();
            }
            $this->assign('user_info',$userInfo);
        }
        $searchTypes = array(
            1   => 'id',
            2   => '昵称'
        );

        $this->assign('searchTypes',$searchTypes);
        return $this->fetch('rank');
    }



    public function modify_user(){
        if(Request::instance()->isPost()){
            $id = input('id',0,'intval');
            if(empty($id)){
                return json(['status'=>'n','msg'=>'操作失败']);
            }
            $name = input('name');
            $opt_type = input('opt_type');
            if(!is_numeric($opt_type)){
                return json(['status'=>'n','msg'=>'请选择修改类型']);
            }
            $opt_val = input('opt_val',0,'intval');
            if(empty($opt_val)){
                return json(['status'=>'n','msg'=>'请输入修改数值']);
            }
            $reason = input('reason');
            if(empty($reason)){
                return json(['status'=>'n','msg'=>'请输入操作原因']);
            }
            $ret = false;
            try{
                $GmApi = new GmUser();
                $ret = $GmApi->modifyProp($id,$opt_type,$opt_val);
                if($ret){
                    $gmAdmin = Session::get('gm_admin');
                    $gameId = isset($gmAdmin['game_id']) ? $gmAdmin['game_id'] : 1;
                    $logData = array(
                        'user_id'   => $id,
                        'user_name' => $name,
                        'opt_type'  => $opt_type,
                        'opt_val'   => $opt_val,
                        'reason'    => $reason,
                        'game_id'   => $gameId,
                        'admin_uid'  => Session::get('gm_admin.id')
                    );
                    model('ModifyUserLog')->data($logData)->save();
                }
            }catch (\Exception $ex){
                return json(['status'=>'n','msg'=>$ex->getMessage()]);
            }
            if($ret){
                return json(['status'=>'y','msg'=>'操作成功']);
            }else{
                return json(['status'=>'n','msg'=>'操作失败']);
            }

            exit;
        }
        $id = input('id',0,'intval');
        $name = input('name',0,'trim');

        $this->assign('id',$id);
        $this->assign('name',$name);

        if(get_game_id() != 5){
            unset($this->modifyUserTypes[5]);
        }
        $this->assign('updateTypes',$this->modifyUserTypes);
        return $this->fetch('modify_user');
    }

    public function modify_user_log(){
        $gmAdmin = Session::get('gm_admin');
        $gameId = isset($gmAdmin['game_id']) ? $gmAdmin['game_id'] : 1;
        $where = "game_id={$gameId}";
        if(!empty($_GET['id'])){
            $where .= " AND user_id = '{$_GET['id']}'";
        }

        if(!empty($_GET['modify_type'])){
            $where .= " AND opt_type = '{$_GET['modify_type']}'";
        }

        if(!empty($_GET['start_date'])){
            $start_time = strtotime($_GET['start_date']);
            $where .= " AND add_time >= '{$start_time}'";
        }

        if(!empty($_GET['end_date'])){
            $end_time = strtotime($_GET['end_date']);
            $where .= " AND add_time <= '{$end_time}'";
        }

        $logModel = model('ModifyUserLog');
        $data = $logModel->where($where)->order('id desc')->paginate(20);
        $page = $data->render();
        $this->assign('page', $page);
        $this->assign('data',$data);
        $this->assign('updateTypes',$this->modifyUserTypes);
        return $this->fetch();
    }

    public function ban_user(){
        if(Request::instance()->isPost()){
            $id = input('id',0,'intval');
            if(empty($id)){
                return json(['status'=>'n','msg'=>'操作失败']);
            }
            $name = input('name');
            $opt_type = input('opt_type',0,'intval');
            if(empty($opt_type)){
                return json(['status'=>'n','msg'=>'请选择操作类型']);
            }
            $reason = input('reason');
            if(empty($reason)){
                return json(['status'=>'n','msg'=>'请输入惩罚原因']);
            }
            $opt_val = 1;
            if($opt_type == 2){
                $ban_time = input('start_date');
                if(empty($ban_time)){
                    return json(['status'=>'n','msg'=>'请输入禁言截止时间']);
                }
                $opt_val = strtotime($ban_time);
            }
            /*
            $start_date = input('start_date');
            $end_date = input('end_date');*/


            switch ($opt_type){
                case 4://解除禁言
                    $opt_type = 2;
                    $opt_val = 0;
                    break;
                case 5://解除封号
                    $opt_type = 3;
                    $opt_val = 0;
            }

            $ret = false;
            try{
                $GmApi = new GmUser();
                $ret = $GmApi->userOp($id,$opt_type,$opt_val);
                if($ret){
                    $gmAdmin = Session::get('gm_admin');
                    $gameId = isset($gmAdmin['game_id']) ? $gmAdmin['game_id'] : 1;
                    $logData = array(
                        'user_id' => $id,
                        'user_name' => $name,
                        'opt_type' => $opt_type,
                        'opt_val' => $opt_val,
                        'game_id' => $gameId,
                        'reason'  => $reason,
                        'admin_uid' => Session::get('gm_admin.id')
                    );
                    model('BanUserLog')->data($logData)->save();
                }
            }catch (\Exception $ex){
                return json(['status'=>'n','msg'=>$ex->getMessage()]);
            }
            if($ret){
                return json(['status'=>'y','msg'=>'操作成功']);
            }else{
                return json(['status'=>'n','msg'=>'操作失败']);
            }

            exit;
        }
        $id = input('id',0,'intval');
        $name = input('name',0,'trim');
        $this->assign('id',$id);
        $this->assign('name',$name);
        $this->assign('updateTypes',$this->banTypes);
        return $this->fetch();
    }

    public function ban_user_log(){
        $gmAdmin = Session::get('gm_admin');
        $gameId = isset($gmAdmin['game_id']) ? $gmAdmin['game_id'] : 1;
        $where = "game_id={$gameId}";
        if(!empty($_GET['id'])){
            $where .= " AND user_id = '{$_GET['id']}'";
        }

        if(!empty($_GET['ban_type'])){
            $where .= " AND opt_type = '{$_GET['ban_type']}'";
        }
        $logModel = model('BanUserLog');
        $data = $logModel->where($where)->order('id desc')->paginate(20);
        $page = $data->render();
        $this->assign('page', $page);
        $this->assign('data',$data);
        $this->assign('updateTypes',$this->banTypes);
        $this->assign('time',time());
        return $this->fetch();
    }

    public function mail(){
        $gmAdmin = Session::get('gm_admin');
        $gameId = isset($gmAdmin['game_id']) ? $gmAdmin['game_id'] : 1;
        $where = "game_id={$gameId}";
        if(!empty($_GET['mail_title'])){
            $where .= " AND mail_title like '%{$_GET['mail_title']}%'";
        }

        if(!empty($_GET['mail_type'])){
            $where .= " AND mail_type like '%{$_GET['mail_type']}%'";
        }

        $logModel = model('MailLog');
        $data = $logModel->where($where)->order('id desc')->paginate(20);
        $page = $data->render();
        $this->assign('page', $page);
        $this->assign('data',$data);
        $this->assign('mail_types',$this->mailTypes);
        return $this->fetch();
    }

    public function send_mail(){
        if(Request::instance()->isPost()){
            $mailType = input('mail_type',1,'intval');
            $title = input('title','','trim');
            $body = input('body','','trim');
            $receive = input('receive',0,'intval');
            $goods = isset($_POST['goods']) ? $_POST['goods'] : false;
            $appendix = array();
            if($goods){
                foreach ($goods as $k=>$v){
                    if(empty($v['goods_id']) || empty($v['goods_count'])){
                        unset($goods[$k]);
                    }
                    $appendix[$v['goods_id']] = intval($v['goods_count']);
                }
            }
            $appendix = $appendix ? json_encode($appendix) : '';
            $ret = false;
            try{
                $GmApi = new GmUser();
                $ret = $GmApi->sendMail($mailType,$title,$body,$receive,$appendix);
                if($ret){
                    $gmAdmin = Session::get('gm_admin');
                    $gameId = isset($gmAdmin['game_id']) ? $gmAdmin['game_id'] : 1;
                    //记录日志
                    $logData = array(
                        'game_id'       => $gameId,
                        'mail_type'     => $mailType,
                        'mail_title'    => $title,
                        'mail_content' => $body,
                        'receive_id'    => $receive,
                        'goods_reward'  => $appendix,
                        'admin_uid'      => Session::get('gm_admin.id')
                    );
                    model('MailLog')->data($logData)->save();
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
        $this->assign('mail_types',$this->mailTypes);
        return $this->fetch();
    }
}
