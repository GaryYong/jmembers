<?php
namespace app\admin\controller;
use app\admin\controller\Admin;
use think\Db;
use util\utilPhp;

class Logs extends Admin
{
    public function gm_log(){
        $this->fetch();
    }

}
