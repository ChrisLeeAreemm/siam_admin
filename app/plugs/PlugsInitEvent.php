<?php

namespace app\plugs;


use app\plugs\curd\Plugs;

class PlugsInitEvent
{
    public static function app_init()
    {
        // TODO 注入路由 [1.获取插件列表(带安装状态) 2.启用插件 3.停用插件 ]

        // TODO 遍历plugs目录，执行已经安装过的plugs init

        (new Plugs())->init();
    }
}