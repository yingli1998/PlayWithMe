<?php 
namespace app\api\controller\v1;

use app\api\controller\Base;
use app\common\lib\Aes;
use app\common\lib\exception\ApiException;
use app\common\model\User;


//客户端auth登录权限基础类
//1.每个接口(需要登录场景)都需要基础它
//2. 判断token是否合法
//3. 用户信息 -> user
class AuthBase extends Base {
    public $user = [];   //用户的基本信息

    public function _initialize(){
        parent::_initialize();
        $this->loginCheck();
        // if (!$this->isLogin()) {
        //     throw new ApiException('您未登录', 401);
        // }
    }

    //判断是否登录
    public function loginCheck(){
        if(empty($this->headers['access_user_token'])){
            throw new ApiException('请求错误', 401);
        }
        $aes = new Aes();
        $str = $aes->decrypt($this->headers['access_user_token']);
        $token = explode('|', $str)[0];   
        if(empty($token)){
            throw new ApiException('请求错误', 401);
        }

        $user = User::get(['token' => $token]);  //根据$token得到用户

        if(empty($user) || $user->status != 1){
            throw new ApiException('用户不存在', 401);
        }

        //判断时间是否过期
        if (time() > $user->time_out) {
            throw new ApiException('登录失效', 401);
        }

        $this->user = $user; //存放到成员变量中
    }
}