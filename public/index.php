<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

require __DIR__ . '/../vendor/autoload.php';
// 你先看看是否安装了哦 Siam 但可以免安装运行curd

if (isset($_GET['green_run'])){
    \setcookie("green_run", $_GET['green_run']);
}
if (!isset($_COOKIE["green_run"])){
    $lock_file = __DIR__. "/install.lock";
    if (!is_file($lock_file)){
        Header("Location:/install.php");
        die;
    }
}

// 执行HTTP应用并响应
$http = (new App())->http;

$response = $http->run();

$response->send();

$http->end($response);
