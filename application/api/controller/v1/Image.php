<?php 
namespace app\api\controller\v1;

use app\api\controller\v1\AuthBase;
use app\common\lib\Aes;

//图片上传接口
class Image extends AuthBase {
    public function save(){
        //print_r($_FILES); 
        $file = request()->file('image');
        $id = $this->user['id'];
        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads' . DS . $id);
        $image = ROOT_PATH . 'public' . DS . 'uploads' . DS . $id . DS . $info->getSaveName();
        $data = ['image' => $image];
        $id = model('User')->save($data, ['id' => $id]);
        return show(config('code.success'), 'OK', $data);
    }
}