<?php

namespace app\plugs\tokenManager;


use app\event\EventTag;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Event;
use think\facade\Route;
use app\facade\SQLiteFacade;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("tokenManager");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "tokenManager",
                'href'   => "/plugs/token_manager/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
        ]);
        return $config;
    }

    public function install()
    {

        $sqlite  = SQLiteFacade::connect();
        $sql =<<<EOF
      CREATE TABLE token_manager
      (id INTEGER PRIMARY KEY   NOT NULL,
      user_identify INTEGER   NOT NULL,
      token           TEXT    NOT NULL,
      create_time           INTEGER     NOT NULL
      );
EOF;
        $sqlite->query($sql);




    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/token_manager/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::any('plugs/token_manager/api/get_list', 'app\plugs\tokenManager\controller\TokenManagerController@get_list');


        // 绑定事件  接管token 注册、注销、验证
        Event::subscribe( 'app\plugs\tokenManager\service\TokenManagerEvent');
    }
}