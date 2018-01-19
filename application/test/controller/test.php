<?php

namespace app\test\controller;
use think\Controller;
use app\common\lib\Rsa;
use app\common\lib\sms\MESSAGEXsend;
use app\common\lib\Aes;


class test extends Controller {
    function rsa(){
        //证书路径
        $pubfile = config('ssl.pubfile');
        $prifile = config('ssl.prifile');

        $string = "MyNameIsMurray";
        $rsa =new Rsa($pubfile,$prifile);
        
        // //创建新的密匙
        // $rsa->buildNewKey($pubfile, $prifile);
        // return show(200,'Key is already ok');

        //加密
        echo "\n\r加密的字符串:\n$string\n\n";
        $x = $rsa->encrypt($string);
        echo "\n生成加密的值:\n$x";

        //解密
        $y = $rsa->decrypt($x);
        echo "\n解密的值:\n$y";
        echo "</pre>";

    }

    function check(){
        //解密文件
        $pubfile = config('ssl.pubfile');
        $prifile = config('ssl.prifile');

        //检查加密是否合理
        //获得header头
        $headers = request()->header();

        //查看header头的各个部分是否为空
        if(empty($headers['sign'])){
            return show(config('code.error'), '没有sign值');
        }

        if(empty($headers['version'])){
            return show(config('code.error'), '没有版本号');
        }
        
        if(empty($headers['app_type'])){
            return show(config('code.error'), '没有app类型');
        }
        
        if(empty($headers['did'])){
            return show(config('code.error'),'没有did');
        }

        //设置sign
        // $sign = setSign($headers['did']);

        //解密
        $rsa =new Rsa($pubfile,$prifile);        
        $real_data = $rsa->decrypt($headers['sign']);

        //授权码验证
        $time = time();
        $did = explode('|', $real_data)[0];
        $request_time = explode('|', $real_data)[1];

        if ($did != $headers['did']) {
            return show(config('code.error'), '授权失败');
        }

        if ($time - (int)$request_time > config('time.sign_time')){
            return show(config('code.error'), '请求超时');
        }

        return show(config('code.success'), '授权成功');
    }

    function getsign(){
        $sign = setSign('12672898363278792i');
        return show(config('code.success'), 'The sign is', $sign);
    }

    public function sendMes(){
        
        $submail=new MESSAGEXsend();
        
        $submail->setTo('17853137126');
        
        $submail->SetProject('BeMMw');
        
        $submail->AddVar('code','198277');
    
        $xsend=$submail->xsend();
        
        return show(config('code.success'), '验证码发送成功', $xsend);    
    }

    public function login(){
        setAppLoginToken();
    }

    //模拟用户端加密
    public function getToken(){
        $data = input('param.token');
        $token = get_access_token($data);
        return show(config('code.success'),'access user token is ', $token);
    }

    //ase加密后的密码
    public function getAes(){
        $data = input('param.password');
        $aes = new Aes();
        $password = setPassword($data);
        $result = $aes->encrypt($password.'|'.time());
        return show(config('code.success'), 'OK', $result);
    }

}