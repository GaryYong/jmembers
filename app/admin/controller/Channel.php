<?php
namespace app\admin\controller;
use app\admin\controller\Admin;

class Channel extends Admin
{
    private $model = null;
    public function __construct()
    {
        parent::__construct();
        $this->model = model('Channels');
        $extConfig = config('ext_config');
        $this->assign('game_ids',$extConfig['game_ids']);
    }

    public function index()
    {
        $channelList = $this->model->select();
        $this->assign('channelList',$channelList);
        return $this->fetch();
    }

    public function add(){
        if($this->request->isPost()){
            $_POST['ctime'] = time();
            $result = $this->model->validate(
                [
                    'game_id' => 'require',
                    'channel_id' => 'require',
                    'channel_name' => 'require'
                ],
                [
                    'game_id.require' => '请选择所属游戏',
                    'channel_id.require' => '请输入渠道id',
                    'channel_name.require' => '请输入渠道名称'
                ]
            )->data($_POST)->save();

            if(false === $result){
                return json(['status'=>'n','msg'=>$this->model->getError()]);
            }
            return json(['status'=>'y','msg'=>'ok']);
        }
        return $this->fetch();
    }

    public function edit(){
        if($this->request->isPost()){
            $_POST['ctime'] = time();
            $id = intval($_POST['id']);
            $result = $this->model->validate(
                [
                    'game_id' => 'require',
                    'channel_id' => 'require',
                    'channel_name' => 'require'
                ],
                [
                    'game_id.require' => '请选择所属游戏',
                    'channel_id.require' => '请输入渠道id',
                    'channel_name.require' => '请输入渠道名称'
                ]
            )->save($_POST,['id'=>$id]);
            if(false === $result){
                return json(['status'=>'n','msg'=>$this->model->getError()]);
            }
            return json(['status'=>'y','msg'=>'ok']);
        }

        $id = $this->request->get('id',0,'intval');
        $data = $this->model->where('id',$id)->find()->toArray();
        if(empty($data)){
            $this->error('非法请求',url('Channel/index'),'',1,[],1);
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function ajax_delete(){
        if($this->request->isPost()){
            $id = $this->request->post('id',0,'intval');
            if(empty($id)){
                return json(['status'=>'n','msg'=>'删除失败！']);
            }
            $flag = $this->model->where(array('id'=>$id))->delete();
            if($flag){
                return json(['status'=>'y','msg'=>'删除成功！']);
            }
        }
        return json(['status'=>'n','msg'=>'删除失败！']);
    }

    public function ajax_check_exists(){
        if($this->request->isPost()){
            $gameId = $this->request->post('game_id',0,'intval');
            $channelId = $this->request->post('channel_id',0,'intval');
            $id = $this->request->post('id',0,'intval');

            if(empty($gameId)){
                return "请选择所属游戏！";
            }

            if(empty($channelId)){
                return "请输入渠道id！";
            }

            $exists = $this->model->checkExists($gameId,$channelId,$id);
            if($exists){
                return "渠道id已存在！";
            }
            return "true";
        }
    }


}
