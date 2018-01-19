<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//Test path
Route::rule('test/rsa','test/test/rsa');
Route::Post('test/check','test/test/check');
Route::rule('test/sendMes', 'test/test/sendMes');
Route::rule('test/login','test/test/login');
Route::rule('test/token', 'test/test/getToken');
Route::rule('test/password', 'test/test/getAes');


//短信验证码的路由
Route::resource('api/:ver/identify', 'api/:ver.Identify');

//登录的路由
Route::post('api/:ver/login', 'api/:ver.login/save');

//获取用户信息的路由
Route::resource('api/:ver/user','api/:ver.user');

//图片上传的路由
Route::post('api/:ver/image', 'api/:ver.image/save');

//判断用户名是否存在
Route::post('api/:ver/username', 'api/:ver.unique/username');
Route::post('api/:ver/corporname', 'api/:ver.unique/corporname');
Route::post('api/:ver/actname', 'api/:ver.unique/activityName');


//创建社团
Route::post('api/:ver/create', 'api/:ver.CreateCorporation/save');

//获取社团, 修改社团信息
Route::resource('api/:ver/corporation','api/:ver.corporation');

//创建活动
Route::post('api/:ver/createActivity', 'api/:ver.CreateActivity/save');

//获取活动, 修改活动信息
Route::resource('api/:ver/activity','api/:ver.activity');








