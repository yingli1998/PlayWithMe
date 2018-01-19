<?php 
namespace app\api\controller\v1;

use app\api\controller\v1\AuthBase;
use app\common\lib\Aes;

class CreateCorporation extends AuthBase {
    public function save(){
        if (!request()->isPost()) {
            return show(config('code.error'),'请求方式不正确', [], 403);
        }

        $info = input('param.'); //传用户名来创建社团

        if (empty($info['username']) and empty($info['cate']) and empty($info['intro'])) {
            return show(config('code.error'), '请求错误', [], 403);
        }

        $founder = model('User')->get(['username' => $info['username']]);

        $data = [
            "founder" => $founder['id'],
            "category" => $info['cate'],
            "introduction" => $info['intro'],
            "member" => (string)$founder['id'],
            "name" => $info['username']
        ];

        if(!empty($info['notice'])){
            $data['notice'] = $info['notice'];
        }

        if (!empty($info['name'])) {
            $data['name'] = $info['name'];
        }

        
        $id = model('Corporation')->add($data);
        
        if($id){
            if(empty($founder['manage_corporation'])){
                $user_data = [
                    'manage_corporation' => $id,
                ];
            }else{
                $user_data = [
                    'manage_corporation' => $founder['manage_corporation'].'|'.$id,
                ];
            }
            if(empty($founder['attend_corporation'])){
                $user_data['attend_corporation'] = $id;
            }else{
                $user_data['attend_corporation'] = $founder['attend_corporation'].'|'.$id;
            }
            model('User')->save($user_data, ['id' => $founder['id']]);
            return show(config('code.success'), "社团创建成功");
        }

        return show(config('code.error'), '社团创建失败', [], 403);
    }
}