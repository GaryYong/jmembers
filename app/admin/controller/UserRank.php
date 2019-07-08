<?php
namespace app\admin\controller;
use app\admin\controller\Admin;
use think\Db;

class UserRank extends Admin{

    private $modifyUserTypes = [  0 => '金币',1 => '充值点数',2 => '摇钱树点数',3=>'喇叭'];
    private $banTypes = [ 2 => '禁言',3 => '封号',4 => '解除禁言',5 => '解除封号'];
    private $mailTypes = [1=>'普通邮件',2=>'系统公告',3=>'活动邮件'];

    private $rankTypes = [1=>'下注/赢取',2=>'奖励',3=>'赠送',4=>'其他'];

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
            $this->assign('money_info',$userInfo);
        }
        $moneyLogModel = model('MoneyLog');
        $where = [];
        $list = $moneyLogModel->where($where)->paginate(10);
        $page = $list->render();

        $this->assign('list',$list);
        $this->assign('page',$page);
        $this->assign('moneyChangeTypes',$this->moneyChangeTypes);
        return $this->fetch('index');
    }
}
