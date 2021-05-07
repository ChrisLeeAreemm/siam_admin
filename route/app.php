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
// 定时任务 url访问兼容
Route::any('/cron/:cron_name/run', function(){
    $request = request();
    $cron_name = \think\helper\Str::studly($request->param('cron_name'));

    $class_namespace = "app\\cron\\$cron_name";
    if (!class_exists($class_namespace)){
        return "class not exists";
    }


    /** @var \app\cron\CronBase $class */
    $class = new $class_namespace;
    $class->run_mode = 'url';
    $class->run();

    return "";
});

Route::any(':module/:controller/:action', \app\handle\RouterDispatch::class);

