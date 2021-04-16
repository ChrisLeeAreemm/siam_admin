<?php

namespace app\plugs\base;


use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{
    
    
    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("base");
        $config->setIcon("");
        $config->setHandleModule(["admin","plugs"]);// 只有admin,plugs模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "base",
                'href'   => "/plugs/base/index",
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
    
        Route::group(function () {
            Route::get('plugs/base/install', 'PlugsBaseController@install');
            Route::get('plugs/base/status', 'PlugsBaseController@status');
            Route::get('plugs/base/uninstall', 'PlugsBaseController@uninstall');
        })->prefix('\app\plugs\base\controller\\');
        
    }
}