<?php

namespace app\plugs;


use app\plugs\curd\Plugs;

class PlugsInitEvent
{
    public static function app_init()
    {

        // TODO 遍历plugs目录，查看是否已经安装
        // 执行已经安装过的plugs init

        (new Plugs())->init();
    }
}