<?php

namespace app\plugs\curd;


use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;

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
    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由 等事件
    }
}