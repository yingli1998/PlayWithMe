<?php

namespace app\api\controller\v1;

use app\api\controller\Base;
use think\Cookie;

//获取验证码的接口
class Identify extends Base {

    //post  设置短信验证码
    public function save(){

        if (!request()->isPost()) {
            return show(config('code.error'),'请求方式不正确', [], 403);
        }

        //检验数据
        $validate = validate('Identify');
        if (!$validate->check(input('post.'))) {
            return show(config('code.error'), $validate->getError(),[], 403);
        }

        //发送短信验证码
        $phone = input('param.phone');
        $code = rand(1000,10000);  //生成验证码
        if(!senMes($phone, $code)){
            return show(config('code.error'), '短信发送失败', [], 403);
        }else{
            Cookie::set($phone, $code, config('time.sms_time')); //验证码存储在cookie中, 并设置有效时间
            return show(config('code.success'), 'OK');
        }
        
    }
}
