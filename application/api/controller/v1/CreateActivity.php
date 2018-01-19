<?php 
namespace app\api\controller\v1;

use app\api\controller\v1\AuthBase;
use app\common\lib\Aes;

class CreateActivity extends AuthBase {
    public function save(){
        if (!request()->isPost()) {
            return show(config('code.error'),'请求方式不正确', [], 403);
        }

        $info = input('param.'); //传用户名来创建社团

        if (empty($info['founder']) and empty($info['corporation']) and empty($info['name'] and empty($info['time'])) and empty($info['address'])) {
            return show(config('code.error'), '请求错误', [], 403);
        }

        $founder = model('User')->get(['username' => $info['username']]);
        $corp = model('Corporation')->get(['name' => $info['corporation']]);

        $data = [
            "founder" => $founder['id'],
            "corporation" => $corp['id'],
            "name" => $info['name'],
            "attender" => (string)$founder['id'],
            "time" => $info['time'],
            "address" => $info['address']
        ];

        $id = model('Activity')->add($data);
        
        if($id){
            if(empty($corp['activity'])){
                $cor_data = [
                    'activity' => $id,
                ];
            }else{
                $cor_data = [
                    'activity' => $corp['activity'].'|'.$id,
                ];
            }

            if(empty($founder['activity'])){
                $user_data = [
                    'activity' => $id,
                ];
            }else{
                $user_data = [
                    'activity' => $founder['activity'].'|'.$id,
                ];
            }

            model('Corporation')->save($cor_data, ['id' => $corp['id']]);
            model('User')->save($user_data, ['id' => $founder['id']]);

            return show(config('code.success'), "活动创建成功");
        }

        return show(config('code.error'), '活动创建失败', [], 403);
    }
}