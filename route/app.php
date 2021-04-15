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
use think\facade\Route;
//User
Route::any('admin/users/get_list', 'admin.AdminUsersController/get_list');
Route::any('admin/users/edit', 'admin.AdminUsersController/edit');
Route::any('admin/users/add', 'admin.AdminUsersController/add');
Route::any('admin/users/get_one', 'admin.AdminUsersController/get_one');
Route::any('admin/users/delete', 'admin.AdminUsersController/delete');

//Roles
Route::any('admin/roles/get_list', 'admin.AdminRolesController/get_list');
Route::any('admin/roles/edit', 'admin.AdminRolesController/edit');
Route::any('admin/roles/add', 'admin.AdminRolesController/add');
Route::any('admin/roles/get_one', 'admin.AdminRolesController/get_one');
Route::any('admin/roles/delete', 'admin.AdminRolesController/delete');

//Auths
Route::any('admin/auths/get_list', 'admin.AdminAuthsController/get_list');
Route::any('admin/auths/edit', 'admin.AdminAuthsController/edit');
Route::any('admin/auths/add', 'admin.AdminAuthsController/add');
Route::any('admin/auths/get_one', 'admin.AdminAuthsController/get_one');
Route::any('admin/auths/delete', 'admin.AdminAuthsController/delete');


Route::any('admin/system/get_list', 'admin.AdminSystemController/get_list');
Route::any('admin/system/get_plugs', 'admin.AdminSystemController/get_plugs');
