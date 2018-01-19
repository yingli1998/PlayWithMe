<?php
use app\common\lib\Rsa;
use app\common\lib\Aes;
use app\common\lib\sms\MESSAGEXsend;

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function show($status, $message, $data=[],  $httpcode=200){
    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];
    return json($data, $httpcode);
}

//设置密码加密
function setPassword($data){
    return md5($data.config('code.password_pre_halt'));
}

//模拟客户端加密sign
function setSign($did){
    //加密形式  did|date
    $time = time();
    print($time);
    echo "\n";
    $str = $did.'|'.$time;
    //加密
    $pubfile = config('ssl.pubfile');
    $prifile = ROOT_PATH.'ssl.prifile';
    $rsa =new Rsa($pubfile,$prifile);

    echo "\n\r加密的字符串:\n$str\n\n";
    $sign = $rsa->encrypt($str);
    echo "\n生成加密的值:\n$sign";

    return $sign;
}

//模拟用户端加密token
function get_access_token($token){
    $aes = new Aes();
    $access_token = $aes->encrypt($token."|".time());
    return $access_token;
}

//发送短信验证码
function senMes($phone, $code){
    $submail=new MESSAGEXsend();
    
    $submail->setTo($phone);
    
    $submail->SetProject(config('sms.project_id'));
    
    $submail->AddVar('code',$code);

    $xsend=$submail->xsend();
    
    if($xsend['status'] == 'success'){
        return true;
    }else{
        return false;
    }
}

//唯一性的token算法
function setAppLoginToken($phone = ''){
    $str = md5(uniqid(microtime(true), true));
    $str = sha1($str.$phone);  //40位
    return $str;
}