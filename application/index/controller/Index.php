<?php
namespace app\index\controller;

class Index
{
    public function hello()
    {
        $hello = show(0,'lalala');
        return $hello;
    }
}
