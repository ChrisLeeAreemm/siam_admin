<?php

namespace app\plugs;

use think\Exception;
use think\helper\Str;

class PlugsInitEvent
{
    public static function app_init()
    {
        //遍历plugs目录,执行已经安装过的plugs init
        $arr        = scandir(__DIR__);
        
        //检查start.plugs文件
        $startPlugs = __DIR__ . '\\' . 'start.plugs';
        if (!is_file($startPlugs)) {
            throw new Exception('start.plugs不存在', 400);
        }
        
        //检查是否在start文件，是否有安装记录
        $startArr = json_decode(file_get_contents($startPlugs), true);
        foreach ($arr as $dirName) {
            //插件根目录
            $path = __DIR__ . '\\' . $dirName;
            //过滤插件文件夹
            if (Str::contains($path, '.') == false && is_dir($path)) {
                
                //检查install.lock文件
                $installFile = $path . '\\' . 'install.lock';
                if (!is_file($installFile)) {
                    continue;
                }
                
                //new Plugs.php 实例
                $PlugsClass = __NAMESPACE__ . '\\' . $dirName . '\Plugs';
                $plugs      = new $PlugsClass();
                
                //获取插件名检索 start.plugs 文件
                $name       = $plugs->get_config()->getName();
                if (!array_key_exists($name, $startArr)) {
                    continue;
                }
                
                //执行init
                $plugs->init();
            }
            
        }
    }
    
}