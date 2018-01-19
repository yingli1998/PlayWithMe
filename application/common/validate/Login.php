<?php 
namespace app\common\validate;

use think\Validate;

class Login extends Validate {

    protected $rule = [
        'phone' => 'require|number|length:11',  //验证手机号
        // 'code'  => 'number|length:4',   //验证码
    ];
}