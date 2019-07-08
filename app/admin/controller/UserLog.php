<?php
namespace app\admin\controller;
use app\admin\controller\Admin;
use app\admin\model\AdminLog;
use think\Db;
use Api\GmUser;

class UserLog extends Admin
{
    private $moneyChangeTypes = [1 => '下注/赢取', 2 => '奖励', 3 => '赠送', 4 => '其他'];
    private $rankTypes = [
        1 => '金币榜',
        2 => 'vip榜',
        3 => '充值榜',
        5 => '现有红包券榜',
        6 => '累计红包券榜',
        7 => '月度微信红包兑换榜',
        101 => '森林舞会豪胜榜',
        102 => '龙凤斗豪胜榜',
        103 => '天地玄黄豪胜榜',
        104 => '时时彩豪胜榜',
        //201 => '森林舞会昨日豪胜榜',
        //202 => '红黑（龙凤）斗昨日豪胜榜',
        //203 => '天地玄黄昨日豪胜榜'
    ];
    private $tbTypes = [1 => '森林舞会', 2 => '龙凤斗', 3 => '天地玄黄', 1000 => '时时乐'];
    private $orderStatus = [1 => '未支付', 2 => '已支付'];
    private $redSourceType = [
        1 => '邮件附件',
        2 => 'GM操作',
        3 => '打开红包',
        4 => '商品兑换',
        5 => '系统退还'
    ];
    private $sources = [
        0 =>'新手红包',
        100 =>'斗地主新手场',
        101 =>'斗地主初级场',
        102 =>'斗地主中级场',
        103 =>'斗地主高级场',
        104 =>'斗地主新手场（不洗牌',
        105 =>'斗地主初级场（不洗牌）',
        106 =>'斗地主中级场（不洗牌）',
        107 =>'斗地主高级场（不洗牌）',
        110 =>'标准三张新手场',
        111 =>'标准三张进阶场',
        112 =>'标准三张初级场',
        113 =>'标准三张中级场',
        114 =>'标准三张高级场',
    ];

    /**
     * 金币变化
     * @return mixed
     */
    public function moneyInfo()
    {
        $list = $page = '';
        if (isset($_GET['do_submit'])) {
            $userId = input('user_id', 0, 'intval');
            $startTime = input('start_date');
            $endTime = input('end_date');
            $startTime = $startTime ? strtotime($startTime) : '';
            $endTime = $endTime ? strtotime($endTime) : '';
            $moneyLogModel = model('MoneyLog');
            if ($userId) {
                $moneyLogModel->where('actorid', $userId);
            }

            if ($startTime) {
                $moneyLogModel->where('time', '>=', $startTime);
            }

            if ($endTime) {
                $moneyLogModel->where('time', '<=', $endTime);
            }
            $list = $moneyLogModel->order('time desc')->paginate(50);
            $page = $list->render();
        }

        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('moneyChangeTypes', $this->moneyChangeTypes);
        return $this->fetch('money_info');
    }


    /**
     * 排行榜
     * @return mixed
     */
    public function rank()
    {
        $data = false;
        if (isset($_GET['do_submit'])) {
            $search_type = input('search_type', 0, 'intval');
            $date = input('start_date');
            $key = input('key');
            $userInfo = false;
            try {
                $GmApi = new GmUser();
                $data = $GmApi->rank($search_type, $date);
            } catch (\Exception $ex) {
                $data = $ex->getMessage();
            }
        }
        $this->assign('data', $data);
        $gameChannelList = model('Channels')->getMapping();
        $this->assign('rankTypes', $this->rankTypes);
        $this->assign('channel_list', isset($gameChannelList[get_game_id()]) ? $gameChannelList[get_game_id()] : []);
        $table_title = '排行榜金币量/充值金额(元)';
        if (!empty($_GET['search_type'])) {
            if ($_GET['search_type'] == 5) {
                $table_title = '现有红包券(元)';
            } else if ($_GET['search_type'] == 6) {
                $table_title = '累计获取红包券(元)';
            } else if ($_GET['search_type'] == 7) {
                $table_title = '本月累积兑换微信红包数额(元)';
            }
        }
        $this->assign('table_title', $table_title);
        return $this->fetch('rank');
    }

    /**
     * 金币赠送
     * @return mixed
     */
    public function giftMoney()
    {
        $page = $list = '';
        if (isset($_GET['do_submit'])) {
            $userId = input('user_id', 0, 'intval');
            $startTime = input('start_date');
            $endTime = input('end_date');
            $startTime = $startTime ? strtotime($startTime) : '';
            $endTime = $endTime ? strtotime($endTime) : '';

            $model = model('Present');
            if ($userId) {
                $model->where('actorid', $userId)->whereOr('taractorid', $userId);
            }

            if ($startTime) {
                $model->where('time', '>=', $startTime);
            }

            if ($endTime) {
                $model->where('time', '<=', $endTime);
            }

            $list = $model->order('time desc')->paginate(50);
            $page = $list->render();
        }

        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('moneyChangeTypes', $this->moneyChangeTypes);
        return $this->fetch('gift_money');
    }


    public function pay()
    {
        $page = $list = '';
        if (isset($_GET['do_submit'])) {
            $userId = input('user_id', 0, 'intval');
            $plattransid = input('plattransid', '', 'trim');
            $startTime = input('start_date');
            $endTime = input('end_date');
            $startTime = $startTime ? strtotime($startTime) : '';
            $endTime = $endTime ? strtotime($endTime) : '';
            $orderStatus = input('order_status');
            if ($orderStatus == 2) {
                $model = model('Pay');
            } else {
                $model = model('Order');
            }


            if ($userId) {
                $model->where('actorid', $userId);
            }

            if ($plattransid) {
                $model->where('plattransid', $plattransid);
            }

            if ($startTime) {
                $model->where('time', '>=', $startTime);
            }

            if ($endTime) {
                $model->where('time', '<=', $endTime);
            }

            $list = $model->order('time desc')->paginate(50);
            $page = $list->render();
        }

        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('orderStatus', $this->orderStatus);
        $this->assign('payType', [0 => '未知', 1 => '爱贝']);
        return $this->fetch('pay');
    }


    public function bets()
    {
        $page = $list = '';
        if (isset($_GET['do_submit'])) {
            $userId = input('user_id', 0, 'intval');
            $startTime = $_GET['start_date'];
            $endTime = $_GET['end_date'];
            $startTime = $startTime ? strtotime($startTime) : '';
            $endTime = $endTime ? strtotime($endTime) : '';
            $tbType = input('tb_type');
            $model = model('Betdetail');
            $where = "1=1";
            if ($tbType) {
                $where .= " AND type = {$tbType}";
            }

            if ($userId) {
                $where .= " AND actorid = {$userId}";
            }


            if ($startTime) {
                $where .= " AND `time` >= {$startTime}";
            }

            if ($endTime) {
                $where .= " AND `time` <= {$endTime}";
            }

            $list = $model->where($where)->order('time desc')->paginate(50);
            $page = $list->render();
        }

        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('orderStatus', $this->orderStatus);
        $this->assign('tbTypes', $this->tbTypes);
        return $this->fetch('bets');
    }

    /**
     * 红包券日志变化查询
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function redticket()
    {
        $page = $list = '';
        if (isset($_GET['do_submit'])) {
            $userId = input('user_id', 0, 'intval');
            $startTime = $_GET['start_date'];
            $endTime = $_GET['end_date'];
            $startTime = $startTime ? strtotime($startTime) : '';
            $endTime = $endTime ? strtotime($endTime) : '';
            $sourceType = input('source_type');
            $model = model('Redticket');
            $where = "1=1";
            if ($sourceType) {
                $where .= " AND source = {$sourceType}";
            }

            if ($userId) {
                $where .= " AND actorid = {$userId}";
            }


            if ($startTime) {
                $where .= " AND `time` >= {$startTime}";
            }

            if ($endTime) {
                $where .= " AND `time` <= {$endTime}";
            }

            $list = $model->where($where)->order('time desc')->paginate(50);
            $page = $list->render();
        }

        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('orderStatus', $this->orderStatus);
        $this->assign('redSourceType', $this->redSourceType);
        return $this->fetch('redticket');
    }


    /**
     * 红包券兑换记录查询
     * @return mixed
     * @throws \think\exception\DbException
     */
    public function redticketuse()
    {
        $page = $list = '';
        if (isset($_GET['do_submit'])) {
            $userId = input('user_id', 0, 'intval');
            $startTime = $_GET['start_date'];
            $endTime = $_GET['end_date'];
            $startTime = $startTime ? strtotime($startTime) : '';
            $endTime = $endTime ? strtotime($endTime) : '';
            $model = model('Redticketuse');
            $where = "1=1";

            if ($userId) {
                $where .= " AND actorid = {$userId}";
            }


            if ($startTime) {
                $where .= " AND `time` >= {$startTime}";
            }

            if ($endTime) {
                $where .= " AND `time` <= {$endTime}";
            }

            $list = $model->where($where)->order('time desc')->paginate(50);
            $page = $list->render();
        }

        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('orderStatus', $this->orderStatus);
        $this->assign('redSourceType', $this->redSourceType);
        return $this->fetch('redticketuse');
    }


    //getUserRedPack

    public function query_userredpack()
    {
        $data = false;
        if (isset($_GET['do_submit'])) {
            $user_id = input('user_id');
            $data = false;
            try {
                $GmApi = new GmUser();
                $data = $GmApi->getUserRedPack($user_id);
            } catch (\Exception $ex) {
                $data = $ex->getMessage();
            }
        }

        $this->assign('data', $data);
        $this->assign('sourceType',$this->sources);
        return $this->fetch('user_red_pack');
    }

    public function ajax_del_user_readpack(){
        if($this->request->isPost()){
            $user_id = input('post.user_id',0,'intval');
            $source = input('post.source',0,'intval');
            $num = input('post.num',0,'intval');
            try {
                $GmApi = new GmUser();
                $data = $GmApi->delUserRedPack($user_id,$source);
            } catch (\Exception $ex) {
                $data = $ex->getMessage();
            }
            $source = $this->sources[$source];
            model('AdminLog')->saveLog("删除用户：{$user_id},红包类型：{$source},数量：{$num}",AdminLog::DEL_USER_RED_PACK_TYPE);
        }
        return json(['status'=>'n','msg'=>'删除失败！']);
    }

}
