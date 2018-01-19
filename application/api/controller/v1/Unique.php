<?php 
namespace app\api\controller\v1;

use app\api\controller\v1\AuthBase;
use app\common\lib\Aes;
use app\common\model\User;


//判断用户名是否存在
class Unique extends AuthBase {
    public function username(){
        $post = input('param.');
        if (empty($post['username'])) {
            return show(config('code.error'), '请填写用户名');
        }
        if(User::get(['username' => $post['username']])){
            return show(config('code.error'), '用户名已存在');
        }
        return show(config('code.success'), '用户名可用');
    }

    public function corporname(){
        $post = input('param.');
        if (empty($post['name'])) {
            return show(config('code.error'), '请填写社团名');
        }
        if(model('Corporation')->get(['name' => $post['name']])){
            return show(config('code.error'), '社团名已存在');
        }
        return show(config('code.success'), '社团名可用');
    }

    public function activityName(){
        $post = input('param.');
        if (empty($post['name'])) {
            return show(config('code.error'), '请填写活动名');
        }
        if(model('Activity')->get(['name' => $post['name']])){
            return show(config('code.error'), '活动名已存在');
        }
        return show(config('code.success'), '活动名可用');
    }

}