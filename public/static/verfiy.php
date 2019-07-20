<?php
session_start();
/**
 * Created by PhpStorm.
 * User: JLB9858
 * Date: 2016/10/23
 * Time: 13:19
 */
class utilImage{
    private $width;//宽度
    private $height; //高度
    private $codeNum;//验证码字符数量
    private $image;//验证码图像资源
    private $sessionKey;//session中保存的名字
    private $captcha;//验证码字符串
    const charWidth = 10;//单个字符宽度,根据输出字符大小而变

    /**
     * 创建验证码类，初始化相关参数
     * @param  $width 图片宽度
     * @param  $height 图片高度
     * @param  $codeNum 验证码字符数量
     * @param  $sessionKey session中保存的名字
     */
    function __construct($width = 50, $height = 20, $codeNum = 4, $sessionKey = 'captcha') {
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
        $this->sessionKey = $sessionKey;

        //保证最小高度和宽度
        if($height < 20) {
            $this->height = 20;
        }
        if($width < ($codeNum * self::charWidth + 10)) {//左右各保留5像素空隙
            $this->width = $codeNum * self::charWidth + 10;
        }
    }

    /**
     * 构造并输出验证码图片
     */
    public  function buildAndExportImage() {
        $this->createImage();
        $this->setDisturb();
        $this->setCaptcha();
        $this->exportImage();
    }

    /**
     * 构造图像，设置底色
     */
    private function createImage() {
        //创建图像
        $this->image = imagecreatetruecolor($this->width, $this->height);
        //创建背景色
        $bg = imagecolorallocate($this->image, mt_rand(220, 255), mt_rand(220, 255), mt_rand(220, 255));
        //填充背景色
        imagefilledrectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $bg);
    }

    /**
     * 设置干扰元素
     */
    private function setDisturb() {

        //设置干扰点
        for($i = 0; $i < 150; $i++) {
            $color = imagecolorallocate($this->image, mt_rand(150, 200),  mt_rand(150, 200),  mt_rand(150, 200));
            imagesetpixel($this->image, mt_rand(5, $this->width - 10), mt_rand(5, $this->height - 3), $color);
        }

        //设置干扰线
        for($i = 0; $i < 10; $i++) {
            $color = imagecolorallocate($this->image, mt_rand(150, 220), mt_rand(150, 220), mt_rand(150, 220));
            imagearc($this->image, mt_rand(-10, $this->width), mt_rand(-10, $this->height), mt_rand(30, 300), mt_rand(20, 200), 55, 44, $color);
        }

        //创建边框色
        $border = imagecolorallocate($this->image, mt_rand(0, 50), mt_rand(0, 50), mt_rand(0, 50));
        //画边框
        imagerectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $border);
    }

    /**
     * 产生并绘制验证码
     */
    private function setCaptcha() {
        $str = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        //生成验证码字符
        for($i = 0; $i < $this->codeNum; $i++) {
            $this->captcha .= $str{mt_rand(0, strlen($str) - 1)};
        }
        //绘制验证码
        for($i = 0; $i < strlen($this->captcha); $i++) {
            $color = imagecolorallocate($this->image, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
            $x = floor(($this->width - 10)/$this->codeNum);
            $x = $x*$i + floor(($x-self::charWidth)/2) + 5;
            if (2 < $this->height - 20) {
                $y = mt_rand(2, $this->height - 20);
            } else {
                $y = mt_rand($this->height - 20, 2);
            }
            imagechar($this->image, 5, $x, $y, $this->captcha{$i}, $color);
        }
    }

    /*
     * 输出图像,验证码保存到session中
     */
    private function exportImage() {
        if(imagetypes() & IMG_GIF){
            header('Content-type:image/gif');
            imagegif($this->image);
        } else if(imagetypes() & IMG_PNG){
            header('Content-type:image/png');
            imagepng($this->iamge);
        } else if(imagetypes() & IMG_JPEG) {
            header('Content-type:image/jpeg');
            imagepng($this->iamge);
        } else {
            imagedestroy($this->image);
            die("Don't support image type!");
        }
        //将验证码信息保存到session中，md5加密
        if(!isset($_SESSION)){
            session_start();
        }
        $_SESSION[$this->sessionKey] = md5($this->captcha);

        imagedestroy($this->image);
    }

    function __destruct() {
        unset($this->width, $this->height, $this->codeNum,$this->captcha);
    }

}

$code=new utilImage(100 , 40);
$code -> buildAndExportImage();
//$code->showImage();   //输出到页面中供 注册或登录使用
//$_SESSION["code"]=$code->getCheckCode();  //将验证码保存到服务器中