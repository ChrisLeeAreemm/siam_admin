<?php

namespace app\plugs\cronDoc;


use app\plugs\errorCode\controller\CronDocController;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("cronDoc");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "cronDoc",
                'href'   => "/plugs/cron-doc/index",
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
        Route::get('plugs/cron-doc/index', function () {
            return $this->pre_render_file(__DIR__."/view/index.html");
        });
        Route::any('plugs/cron-doc/api/get_list', 'app\plugs\cronDoc\controller\CronDocController@get_list');
    }
}