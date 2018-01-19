<?php

namespace app\api\controller;
use think\Controller;
use app\common\lib\Rsa;
use app\common\lib\exception\ApiException;

//用户全局header头的校验, 所有的接口都需要继承它
class Base extends Controller {
    //初始化的时候调用这个方法, 所以, 可以把一些加密的处理放在这个位置
    public function _initialize()
    {
        $this->checkRequestAuth();  //先进行加密检查  若失败则返回授权失败
         
    }

    private function checkRequestAuth(){
        //解密文件
        $pubfile = config('ssl.pubfile');
        $prifile = config('ssl.prifile');

        //检查加密是否合理
        //获得header头
        $headers = request()->header();
        $this->headers = $headers;

        //查看header头的各个部分是否为空
        if(empty($headers['sign'])){
            throw new ApiException('sign为空', 401);
        }

        if(empty($headers['version'])){
            throw new ApiException('版本号为空', 401);
        }
        
        if(empty($headers['app_type'])){
            throw new ApiException('app_type为空', 401);
        }
        
        if(empty($headers['did'])){
            throw new ApiException('did为空', 401);
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
            throw new ApiException('授权失败', 401);
        }

        if ($time - (int)$request_time > config('time.sign_time')){
            throw new ApiException('请求超时', 401);
        }

        return true;
    }
}



