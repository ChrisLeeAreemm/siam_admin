<?php

namespace app\plugs;

use think\helper\Str;

class PlugsInitEvent
{
    public static function app_init()
    {
        //遍历plugs目录,执行已经安装过的plugs init
        $arr = scandir(__DIR__);
        foreach ($arr as $key => $dirName) {
            $path = __DIR__ . '\\' . $dirName;
            if (Str::contains($path, '.') == false && is_dir($path)) {
                $PlugsClass = __NAMESPACE__ . '\\' . $dirName . '\Plugs';
                $plugs      = new $PlugsClass();
                $plugs->init();
            }
        }
    }
}