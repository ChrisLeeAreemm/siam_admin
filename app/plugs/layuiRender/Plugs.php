<?php

namespace app\plugs\layuiRender;


use app\plugs\errorCode\controller\LayuiRenderController;
use app\plugs\layuiRender\test\Test;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("layuiRender");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "layui布局生成器",
                'href'   => "/plugs/layui_render/index",
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
        // 测试用
        // echo ((new Test)->run());
        // die;
        // 在这里注入路由[api] 等事件
        Route::get('plugs/layui_render/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::any('plugs/layui_render/api/get_list', 'app\plugs\layuiRender\controller\LayuiRenderController@get_list');
    }
}