<?php
/**
 * Created by PhpStorm.
 * User: lizhiyong
 * Date: 2017/11/5
 * Time: 上午9:01
 */

namespace app\admin\model;
use app\admin\model\Base;

class ReportData extends Base{
    protected $pk = 'id';
    protected $field = array('log_time','human_date','log_type','channel_id','log_data','ctime');
    protected $table = "report_data";


}