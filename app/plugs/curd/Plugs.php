<?php

namespace app\plugs\curd;


use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("curd");
        $config->setIcon("");
        $config->setHandleModule(["admin"]);// 只有admin模块才会执行初始化
        $config->setHomeView("plugs/curd/index");
        return $config;
    }

    public function install()
    {
        // 把视图移到public/admin/page中
    }

    public function remove()
    {
        // 移除视图 数据库等
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/curd/index', function () {
            return $this->pre_render_file(__DIR__."/view/index.html");
        });
    }
}