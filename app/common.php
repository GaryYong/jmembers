<?php

/************************************************************************
 * 公共函数可以写在这个文件
 */

use think\Config;

if(!function_exists("pr")){
    function pr($params){
        echo "<pre>";
        print_r($params);
        echo "</pre>";
    }
}

if(!function_exists("output")){
    function output($code=-1 , $msg="" , $data = array()){
        echo json_encode(array("errorcode" => $code , "desc" => $msg , "data" => $data));exit(0);
    }
}


if(!function_exists("position")){
    function position($num){
        Config::load(CONF_PATH.'/otherConfig.php');
        $position = Config::get("teacher_position");
        return $position[$num];
    }
}

if(!function_exists("studentYear")){
    function studentYear($year = ""){
        if(empty($year)) $yeData = date("Y" , time());
        $moData = date("m" , time());
        if($moData < 8 ){
            $studentYear = $yeData - 1;
        }else{
            $studentYear = $yeData;
        }
        return $studentYear;
    }
}

function get_page_nav(){
    $Request = \think\Request::instance();
    $controller = $Request->controller();
    $action = $Request->action();

    $MenuModel = new \app\admin\model\Menu();
    $data = $MenuModel->getParentsByControllerAndAction($controller,$action);
    $nav_str = '<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页  __nav_str__<a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>';
    $str = '';
    if($data){
        foreach ($data as $v){
            $str .= '<span class="c-gray en">&gt;</span> '.$v['name'];
        }
        $nav_str = str_replace('__nav_str__',$str,$nav_str);
    }else{
        $nav_str = str_replace('__nav_str__',$str,$nav_str);
    }
    return $nav_str;
}

/**
 * 获取角色名
 * @param $role_id
 * @return mixed|string
 */
function get_role_name($role_id){
    static $roles = array();
    if(empty($roles)){
        $RoleModel = model('Roles');
        $rolesData = $RoleModel->getRoleMap();
        if(!$rolesData){return '未知';}
        $roles = $rolesData;
    }
    $roleName = isset($roles[$role_id]) ? $roles[$role_id] : '未知';
    return $roleName;
}

/**
 * 时间戳转换为可读时间
 * @param $time
 * @param string $format
 * @return false|string|void
 */
function from_unixtime($time,$format='Y-m-d H:i:s'){
    if(empty($time))return ;
    return date($format,$time);
}

function set_web_user_session($value){
    return \think\Session::set('kms_user_sess',$value);
}

function get_web_session(){
    return \think\Session::get('kms_user_sess');
}

function clear_web_session(){
    return \think\Session::delete('kms_user_sess');
}


function get_target_url($url,$var){
    return str_replace('__cid__',$var,$url);
}


function lr_replace($param){
    return str_replace('"',"'",$param);
}

function money_human($money){
    $prec = 2;
    if($money < 1000){
        return $money;
    }else if($money < 10000){
        return round($money/1000,$prec)."千";
    }else if($money < 100000000){
        return round($money/10000,$prec)."万";
    }else if($money < 100000000000){
        return round($money/100000000,$prec)."亿";
    }else{
        return $money;
    }
}

function replace_task_lang($task_key,$collect=0){
    $taskLangs = array(
        'bind_phone' => '绑定手机号',
        'gold'       => '累计赢金%s游戏币',
        'recharge'   => '累计充值%s元',
        'play_count' => '累计局数%s局',
        'tdxh_play_count' => '累计天地玄黄上庄局数%s局',
    );
    return sprintf($taskLangs[$task_key],money_human($collect));
}

function get_admin_name($admin_uid){
    static $adminNames = array();
    if(isset($adminNames[$admin_uid])){
        return $adminNames[$admin_uid];
    }

    $userInfo = model('Users')->where('id',$admin_uid)->field('id,user_name')->find()->toArray();
    if($userInfo){
        $adminNames[$userInfo['id']] = $userInfo['user_name'];
        return $userInfo['user_name'];
    }
    return false;
}


/**
 * 分页函数
 *
 * @param $num 信息总数
 * @param $curr_page 当前分页
 * @param $perpage 每页显示数
 * @param $urlrule URL规则
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 分页
 *
 *
 *
 *
 *
 *
 *
 *
 *
 */
function pages($num, $curr_page, $perpage = 20, $urlrule = '', $array = array(),$setpages = 10) {
    if(defined('URLRULE') && $urlrule == '') {
        $urlrule = URLRULE;
        $array = $GLOBALS['URL_ARRAY'];
    } elseif($urlrule == '') {
        $urlrule = url_par('page={$page}');
    }
    $multipage = '';
    if($num > $perpage) {
        $page = $setpages+1;
        $offset = ceil($setpages/2-1);
        $pages = ceil($num / $perpage);
        if (defined('IN_ADMIN') && !defined('PAGES')) define('PAGES', $pages);
        $from = $curr_page - $offset;
        $to = $curr_page + $offset;
        $more = 0;
        if($page >= $pages) {
            $from = 2;
            $to = $pages-1;
        } else {
            if($from <= 1) {
                $to = $page-1;
                $from = 2;
            }  elseif($to >= $pages) {
                $from = $pages-($page-2);
                $to = $pages-1;
            }
            $more = 1;
        }
        $multipage .= '<li><a class="a1">'.$num.'</a></li>';
        if($curr_page>0) {
            $multipage .= ' <li><a href="'.pageurl($urlrule, $curr_page-1, $array).'" class="a1">上一页</a></li>';
            if($curr_page==1) {
                $multipage .= ' <li class="active"><span>1</span></li>';
            } elseif($curr_page>6 && $more) {
                $multipage .= ' <li><a href="'.pageurl($urlrule, 1, $array).'">1</a></li>..';
            } else {
                $multipage .= ' <li><a href="'.pageurl($urlrule, 1, $array).'">1</a></li>';
            }
        }
        for($i = $from; $i <= $to; $i++) {
            if($i != $curr_page) {
                $multipage .= ' <li><a href="'.pageurl($urlrule, $i, $array).'">'.$i.'</a></li>';
            } else {
                $multipage .= ' <li class="active"><span>'.$i.'</span></li>';
            }
        }
        if($curr_page<$pages) {
            if($curr_page<$pages-5 && $more) {
                $multipage .= ' ..<li><a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a></li> <li><a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="a1">下一页</a></li>';
            } else {
                $multipage .= ' <li><a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a></li> <li><a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="a1">下一页</a></li>';
            }
        } elseif($curr_page==$pages) {
            $multipage .= ' <li class="active"><span>'.$pages.'</span></li> <li><a href="'.pageurl($urlrule, $curr_page, $array).'" class="a1">下一页</a></li>';
        } else {
            $multipage .= ' <li><a href="'.pageurl($urlrule, $pages, $array).'">'.$pages.'</a></li> <li><a href="'.pageurl($urlrule, $curr_page+1, $array).'" class="a1">下一页</a></li>';
        }
    }
    $multipage = "<ul class=\"pagination\">{$multipage}</ul>";
    return $multipage;
}



/**
 * 返回分页路径
 *
 * @param $urlrule 分页规则
 * @param $page 当前页
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 完整的URL路径
 */
function pageurl($urlrule, $page, $array = array()) {
    if(strpos($urlrule, '~')) {
        $urlrules = explode('~', $urlrule);
        $urlrule = $page < 2 ? $urlrules[0] : $urlrules[1];
    }
    $findme = array('{$page}');
    $replaceme = array($page);
    if (is_array($array)) foreach ($array as $k=>$v) {
        $findme[] = '{$'.$k.'}';
        $replaceme[] = $v;
    }
    $url = str_replace($findme, $replaceme, $urlrule);
    $url = str_replace(array('http://','//','~'), array('~','/','http://'), $url);
    return $url;
}

/**
 * URL路径解析，pages 函数的辅助函数
 *
 * @param $par 传入需要解析的变量 默认为，page={$page}
 * @param $url URL地址
 * @return URL
 */
function url_par($par, $url = '') {
    if($url == '') $url = get_url();
    $pos = strpos($url, '?');
    if($pos === false) {
        $url .= '?'.$par;
    } else {
        $querystring = substr(strstr($url, '?'), 1);
        parse_str($querystring, $pars);
        $query_array = array();
        foreach($pars as $k=>$v) {
            if($k != 'page') $query_array[$k] = $v;
        }
        $querystring = http_build_query($query_array).'&'.$par;
        $url = substr($url, 0, $pos).'?'.$querystring;
    }
    return $url;
}


/**
 * 获取当前页面完整URL地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}


/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace('\\','',$string);
    return $string;
}


function getSiteDb(){
    $gameId = get_game_id();
    $cfg = [
        '' => '',
        -1 => '',
        1=>'database.db_gamelog',
        4=>'database.db_gamelog_zzdr',
        5=> 'database.db_gamelog_readpack',
        6 => 'database.db_gamelog_zft',
    ];
    return $cfg[$gameId];
}


function get_game_id(){
    $data = think\Session::get('gm_admin');
    $game_ids = isset($data['game_ids']) ? $data['game_ids'] : [];
    if(empty($data['game_id'])){
        return $game_ids ? current($game_ids) : -1;
    }
    return intval($data['game_id']);
}

function get_actor_name($actor_id){
    $game_id = get_game_id();
    if($game_id == 5){
        $DB = \think\Db::connect('database.db_gamecpl_readpack');
        $result = $DB->query("SELECT `name` FROM actor WHERE id='{$actor_id}';")->toArray();
        return empty($result[0]['name']) ? '' : $result[0]['name'];
    }
    return '';

}