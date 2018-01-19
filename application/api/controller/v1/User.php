<?php 
namespace app\api\controller\v1;

use app\api\controller\v1\AuthBase;
use app\common\lib\Aes;

//获取用户信息  更改用户信息的接口
class User extends AuthBase {
    public function save(){
        return show(config('code.success'), '登录成功');
    }

    //获取用户信息
    //用户的基本信息非常隐私, 需要加密处理
    public function read(){
        $data = [
            'username' => $this->user['username'],
            'phone' => $this->user['phone'],
            'sex' => $this->user['sex'],
            'image' => $this->user['image'],
            'signature' => $this->user['signature'],
            'age'    => $this->user['age'],
            'email' => $this->user['email'],
            'address' => $this->user['address'],
            'attend_corporation' => $this->user['attend_corporation'],
            'activity' => $this->user['activity'],
            'manage_corporation' => $this->user['manage_corporation'],
        ];

        $aes = new Aes();

        return show(config('code.success'), 'OK',$data);
    }

    //修改数据
    public function update(){
        $postData = input('put.');
        $data = [];

        //validate  进行校验
        if (!empty($postData['username'])) {
            $data['username'] = $postData['username'];
        } 

        if (!empty($postData['sex'])) {
            $data['sex'] = $postData['sex'];
        }

        if (!empty($postData['email'])) {
            $data['email'] = $postData['email'];
        }

        if (!empty($postData['age'])) {
            $data['age'] = $postData['age'];
        }

        if (!empty($postData['address'])) {
            $data['address'] = $postData['address'];
        }

        if (!empty($postData['attend_corporation'])) {
            $data['attend_corporation'] = $postData['attend_corporation'];
        }

        if (!empty($postData['activity'])) {
            $data['activity'] = $postData['activity'];
        }

        if (!empty($postData['manage_corporation'])) {
            $data['manage_corporation'] = $postData['manage_corporation'];
        }

        if (!empty($postData['signature'])) {
            $data['signature'] = $postData['signature'];
        }

        if(!empty($postData['password'])){
            $aes = new Aes();
            $password = $aes->decrypt($postData['password']);       
            $data['password'] = setPassword($password);  //在客户端把密码用AES加密处理过之后传输过来, 并在本地加盐md5加密之后存储在数据库中
        }

        if(empty($data)){
            return show(config('code.error'), '数据不合法', [], 403);
        }

        try{
            $id = model('User')->save($data, ['id' => $this->user->id]);
            if ($id) {
                return show(config('code.success'), '修改成功');
            }
        }catch(\Exception $e){
            return show(config('code.error'), $e->getMessage(), [], 403);
        }
        
    }


}