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

class ModifyUserLog extends Base{
    protected $pk = 'id';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'add_time';
    protected $updateTime = false;

    //自定义初始化
    protected function initialize()
    {
        //需要调用`Model`的`initialize`方法
        parent::initialize();
        $this->query("SET NAMES utf8mb4;");
        //TODO:自定义的初始化
    }
}