<?php

namespace app\plugs\—PLUGS—;


use app\plugs\errorCode\controller\—PLUGS—STUDLY—Controller;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("—PLUGS—");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "—PLUGS—",
                'href'   => "/plugs/—PLUGS—SNAKE—/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
        ]);
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
        Route::get('plugs/—PLUGS—SNAKE—/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::any('plugs/—PLUGS—SNAKE—/api/get_list', 'app\plugs\—PLUGS—\controller\—PLUGS—STUDLY—Controller@get_list');
    }
}