<?php 
namespace app\api\controller\v1;

use app\api\controller\v1\AuthBase;
use app\common\lib\Aes;

//获取用户信息  更改用户信息的接口
class Activity extends AuthBase {

    public function save(){
        $name = input('param.name');

        $info = model('Activity')->get(['name' => $name]);
        $founder = model('User')->get(['id'=>$info['founder']]);
        $corporation = model('Corporation')->get(['id'=>$info['corporation']]);

        $data = [
            'founder' => $founder['username'],
            'corporation' => $corporation['name'],
            'name' => $info['name'],
            'time' => $info['time'],
            'address' => $info['address'],
            'attender' => $info['attender'],
            'credit' => $info['credit'],
            'status' => $info['status']
        ];

        return show(config('code.success'), 'OK', $data);
    }

    //修改数据
    public function update(){
        $postData = input('put.');
        $data = [];

        $info =  model('Activity')->get(['name' => $postData['name']]);

        //validate  进行校验
        if (!empty($postData['new_name'])) {
            $data['name'] = $postData['new_name'];
        } 

        if (!empty($postData['time'])) {
            $data['time'] = $postData['time'];
        } 

        if (!empty($postData['address'])) {
            $data['address'] = $postData['address'];
        } 

        if (!empty($postData['credit'])) {
            $data['credit'] = $postData['credit'];
        } 

        if (!empty($postData['status'])) {
            $data['status'] = $postData['status'];
        } 

        if (!empty($postData['attender'])) {
            $member = model('User')->get(['username' => $postData['attender']]);
            $data['attender'] = $info['attender'] . "|" . $member['id'];
            if(empty($member['activity'])){
                $user_data = ['activity' => $info['id']];
            }else{
                $user_data = ['activity' => $member['activity'].'|'.$info['id']];
            }
        
            $userid = model('User')->save($user_data, ['id' => $member['id']]);
            if(empty($userid)){
                return show(config('code.error'), '加入社团失败');
            }
        } 

        if(empty($data)){
            return show(config('code.error'), '数据不合法', [], 403);
        }

        try{
            $id = model('Activity')->save($data, ['id' => $info['id']]);
            if ($id) {
                return show(config('code.success'), '修改成功');
            }
        }catch(\Exception $e){
            return show(config('code.error'), $e->getMessage(), [], 403);
        }
        
    }


}