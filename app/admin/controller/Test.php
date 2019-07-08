<?php
namespace app\admin\controller;
use Api\Request;
use app\admin\controller\Admin;
use think\Config;

/**
 * @author Gary <lizhiyong2204@sina.com>
 * @date 2015年12月14日
 * @todu
 */
class Test extends Admin {

    public function request($data, $url)
    {
        $data = json_decode($data , true);
        $data['sign'] = Request::encryption($data);
        $ret = Request::post($url, $data);
        if(isset($ret["data"]) && !empty($ret["data"])){
            return json_decode($ret["data"] , true);
        }else{
            throw new \Exception(var_export($ret,true));
        }
    }

    public function api(){
        if($this->request->isPost()){
            $domain = input('post.domain');
            $method = input('post.method');
            $param = input('post.param');
            $param = stripcslashes($_POST['param']);
            $param = str_replace("'","\"",$param);
            try{
                $result = $this->request($param,$domain.$method);
            }catch (\Exception $e){
                var_dump($e->getMessage(),$e);exit;
            }
            echo $this->jsonFormat(json_encode($result,JSON_UNESCAPED_UNICODE));
            exit;
        }

        $apiConfigs = Config::get('api_config.api');
        $this->assign('appStatus','test');
        $this->assign('domains',$apiConfigs['domains']);
        $this->assign('api_method',$apiConfigs['method']);
        return $this->fetch('api2');
    }

    public function tokenList(){
        $cplConfigs = Config::get('cpl_channel');
        $this->assign('cplConfigs',$cplConfigs);
        return $this->fetch('token');
    }


    private function jsonFormat($data, $indent=null){
        //$data = json_encode($data);
        $data = stripcslashes($data);
        $data = urldecode($data);
        $ret = '';
        $pos = 0;
        $length = strlen($data);
        $indent = isset($indent)? $indent : '&nbsp;&nbsp;&nbsp;&nbsp;';
        $newline = "<br>";
        $prevchar = '';
        $outofquotes = true;

        for($i=0; $i<=$length; $i++){
            $char = substr($data, $i, 1);
            if($char=='"' && $prevchar!='\\'){
                $outofquotes = !$outofquotes;
            }elseif(($char=='}' || $char==']') && $outofquotes){
                $ret .= $newline;
                $pos --;
                for($j=0; $j<$pos; $j++){
                    $ret .= $indent.'&nbsp;';
                }
            }
            $ret .= $char;
            if(($char==',' || $char=='{' || $char=='[') && $outofquotes){
                $ret .= $newline;
                if($char=='{' || $char=='['){
                    $pos ++;
                }
                for($j=0; $j<$pos; $j++){
                    $ret .= $indent;
                }
            }
            $prevchar = $char;
        }

        return $ret;
    }

}