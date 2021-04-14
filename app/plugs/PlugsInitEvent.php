<?php

namespace app\plugs;


// use app\plugs\errorCode\Plugs;
use app\plugs\curd\Plugs;

class PlugsInitEvent
{
    public static function app_init()
    {
        // TODO 遍历plugs目录，执行已经安装过的plugs init

        (new Plugs())->init();
    }
}