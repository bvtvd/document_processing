<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

// 首页
Route::get('/', 'index/Document/index');
Route::post('/', 'index/Document/handle'); // 文档处理

// 商家管理列表
Route::get('/merchant/:id', 'index/Merchant/edit'); // 编辑页面
Route::put('/merchant/:id', 'index/Merchant/update'); // 修改提交
Route::delete('/merchant/:id', 'index/Merchant/destroy'); // 删除
Route::get('/merchant/create', 'index/Merchant/create');  // 新增页面
Route::post('/merchant/create', 'index/Merchant/store');  // 新增提交
Route::get('/merchant', 'index/Merchant/index');

// 收单员管理
Route::get('/collector/:id', 'index/Collector/edit'); // 编辑页面
Route::put('/collector/:id', 'index/Collector/update'); // 修改提交
Route::delete('/collector/:id', 'index/Collector/destroy'); // 删除
Route::get('/collector/create', 'index/Collector/create');  // 新增页面
Route::post('/collector/create', 'index/Collector/store');  // 新增提交
Route::get('/collector', 'index/Collector/index'); // 列表


