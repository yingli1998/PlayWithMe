<?php 
namespace app\common\validate;

use think\Validate;

class Identify extends Validate {

    protected $rule = [
        'phone' => 'require|number|length:11',  //验证手机号
    ];
}