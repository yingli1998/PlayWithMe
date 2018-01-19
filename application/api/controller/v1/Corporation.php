<?php 
namespace app\api\controller\v1;

use app\api\controller\v1\AuthBase;
use app\common\lib\Aes;

//获取用户信息  更改用户信息的接口
class Corporation extends AuthBase {

    public function save(){
        $name = input('param.name');

        $info = model('Corporation')->get(['name' => $name]);

        $data = [
            'level' => $info['level'],
            'category' => $info['category'],
            'name' => $info['name'],
            'introduce' => $info['introduce'],
            'activity' => $info['activity'],
            'notice' => $info['notice'],
            'member' => $info['member']
        ];

        return show(config('code.success'), 'OK', $data);
    }

    //修改数据
    public function update(){
        $postData = input('put.');
        $data = [];

        $info =  model('Corporation')->get(['name' => $postData['name']]);

        //validate  进行校验
        if (!empty($postData['introduce'])) {
            $data['introduce'] = $postData['introduce'];
        } 

        if (!empty($postData['activity'])) {
            $data['activity'] = $postData['activity'];
        } 

        if (!empty($postData['notice'])) {
            $data['notice'] = $postData['notice'];
        } 

        if (!empty($postData['member'])) {
            $member = model('User')->get(['username' => $postData['member']]);
            $data['member'] = $info['member'] . "|" . $member['id'];
            if(empty($member['attend_corporation'])){
                $user_data = ['attend_corporation' => $info['id']];
            }else{
                $user_data = ['attend_corporation' => $member['attend_corporation'].'|'.$info['id']];
            }
        
            $userid = model('User')->save($user_data, ['id' => $member['id']]);
            if(empty($userid)){
                return show(config('code.error'), '加入社团失败');
            }
        } 

        if (!empty($postData['new_name'])) {
            $data['name'] = $postData['new_name'];
        } 

        if(empty($data)){
            return show(config('code.error'), '数据不合法', [], 403);
        }

        try{
            $id = model('Corporation')->save($data, ['id' => $info['id']]);
            if ($id) {
                return show(config('code.success'), '修改成功');
            }
        }catch(\Exception $e){
            return show(config('code.error'), $e->getMessage(), [], 403);
        }
        
    }


}