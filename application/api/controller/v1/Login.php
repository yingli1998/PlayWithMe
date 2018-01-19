<?php 

namespace app\api\controller\v1 ;
use app\api\controller\Base;
use think\Cookie;
use app\common\lib\Rsa;
use app\common\lib\Aes;
use app\common\model\User;


//登录接口
class Login extends Base {

    public function save(){

        if (!request()->isPost()) {
            return show(config('code.error'), '您没有权限', '', 403);
        }

        //获取输入的参数
        $param = input('param.');

        if (empty($param['code']) && empty($param['password'])) {
            return show(config('code.error'), '登录失败');
        }

        //validate做严格校验
        $validate = validate('Login');
        if (!$validate->check($param)) {
            return show(config('code.error'), $validate->getError(),[], 403);
        }

        //若存在验证码, 则属于验证码校验模式
        if(!empty($param['code'])){
            $code = Cookie::get($param['phone']);
            if($code != $param['code']){
                return show(config('code.error'), '验证码错误', '', 403);
            }
        }

        $data = [
            'token' => setAppLoginToken($param['phone']),
            'time_out' => strtotime("+".config('time.login_time_out_day')." days"),
        ];

        //查询这个手机号是否存在
        $user = User::get(['phone' => $param['phone']]);
        if ($user && $user->status == 1) {          
            //更新
            if(!empty($param['password'])){
                //判断用户名的密码和库里的密码是否一致
                if (empty($user['password'])) {
                    return show(config('code.error'), '用户没有设置密码');
                }
                $aes = new Aes();
                $real_data = $aes->decrypt($param['password']);
                $password = explode('|', $real_data)[0];
                $time = explode('|', $real_data)[1];
                if (time() - $time > config('time.password_time')){
                    return show(config('code.error'), '密码已过期');
                }
                if ($password != $user['password']) {
                    return show(config('code.error'), '密码不正确');
                }
            }
           $id = model('User')->save($data, ['phone' => $param['phone']]);
        }else{
            //第一次登录
            if (!empty($param['code'])) {
                $data['username'] = '奶黄包-'.$param['phone'];
                $data['status'] = 1;
                $data['phone'] = $param['phone'];
                //添加到数据库
                $id = model('User')->add($data);    
            }else{
                return show(config('code.error'), '用户不存在');
            }
        }

        if ($id){
            //token加密之后传回客户端
            $aes = new Aes();
            $token= $aes->encrypt($data['token']);
            $result = [
                'token' => $token,
            ];
            return show(config('code.success'), 'OK', $result); //把token返回给客户端
        }else {
            return show(config('code.error'), '登录失败', [], 400);
        }
    }
}