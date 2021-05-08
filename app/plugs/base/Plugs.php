<?php

namespace app\plugs\base;


use app\plugs\base\service\PlugsCommandStatus;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{
    
    
    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("base");
        $config->setHandleModule(["admin","plugs"]);// 只有admin,plugs模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "command运行状态",
                'href'   => "/plugs/base/command_status",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
            [
                'title'  => '安装',
                'href'   => 'page/plugs/base/install.html',
                'icon'   => "fa fa-wrench",
                'target' => '_self',
            ],
            [
                'title'  => '停用',
                'href'   => 'page/plugs/base/status.html',
                'icon'   => "fa fa-unlock-alt",
                'target' => '_self',
            ],
            [
                'title'  => '启用',
                'href'   => 'page/plugs/base/status.html',
                'icon'   => "fa fa-unlock",
                'target' => '_self',
            ],
            [
                'title'  => '编辑',
                'href'   => 'page/plugs/base/edit.html',
                'icon'   => "fa fa-edit",
                'target' => '_self',
            ]
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
            Route::post('plugs/base/edit_plugs', 'PlugsBaseController@edit_plugs');
            Route::any('plugs/base/command_status', function(){
                halt(PlugsCommandStatus::get_command_list());// TODO 做成列表
            });
        })->prefix('\app\plugs\base\controller\\');
        
    }
}