<?php

namespace app\plugs\errorCode;


use app\plugs\errorCode\controller\ErrorCodeController;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("errorCode");
        $config->setIcon("");
        $config->setHandleModule(["admin"]);// 只有admin模块才会执行初始化
        $config->setHomeView("plugs/error-code/index");
        return $config;
    }

    public function install()
    {
    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/error-code/index', function () {
            return $this->pre_render_file(__DIR__."/view/index.html");
        });
        Route::any('plugs/error-code/api/get_list', 'app\plugs\errorCode\controller\ErrorCodeController@get_list');
    }
}