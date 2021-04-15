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

use think\route\dispatch\Url;
use think\route\RuleGroup;
use think\route\RuleItem;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
// 执行HTTP应用并响应
$http = $app->http;

// /** @var \think\Request $request */
// $request = $app->make('request', [], true);
// $request->setController('test');
// $request->setAction('test');
// $app->instance('request', $request);

$response = $http->run();

$response->send();

$http->end($response);
