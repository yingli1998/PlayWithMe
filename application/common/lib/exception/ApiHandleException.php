<?php 
namespace app\common\lib\exception;
use think\exception\Handle;

class ApiHandleException extends Handle {
    public $httpCode = 500;
    
    public function render(\Exception $e){
        if (config('app_debug')==true) {
            return parent::render($e);  //如果调试开启, 则用父类的方法处理
        }

        if ($e instanceof ApiException) {
            $this->httpCode = $e->httpCode;      //如果错误属于API, 则自己处理
        }

        return  show(0, $e->getMessage(), [], $this->httpCode);
        
    }
}