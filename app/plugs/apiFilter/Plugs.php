<?php

namespace app\plugs\apiFilter;


use app\facade\SiamApp;
use app\plugs\apiFilter\service\ApiFilterCommand;
use app\plugs\errorCode\controller\ApiFilterController;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\App;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("apiFilter");
        $config->setHandleModule(["api", "plugs", "cli"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "apiFilter",
                'href'   => "/plugs/api_filter/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
        ]);
        return $config;
    }

    public function install()
    {
        // TODO 创建token限流规则表
    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/api_filter/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::any('plugs/api_filter/api/get_list', 'app\plugs\apiFilter\controller\ApiFilterController@get_list');

        // 注入自定义命令行
        $console = SiamApp::getInstance()->getConsole();
        if ($console){
            $console->addCommand(new ApiFilterCommand(), 'api-filter');
        }
    }
}