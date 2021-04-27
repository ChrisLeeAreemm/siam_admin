<?php

namespace app\plugs\dbHelper;


use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("dbHelper");
        $config->setHandleModule(["admin","plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "数据表字典", //数据表字典
                'href'   => "/plugs/db_helper/db_dict/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
            [
                'title'  => "数据表优化", //数据表优化
                'href'   => "/plugs/db_helper/db_optimize/index",
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
        Route::get('plugs/db_helper/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });

        // 字典index页面
        Route::get('plugs/db_helper/db_dict/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/dbDict/index.html");
        });
        // 数据表优化index
        Route::get('plugs/db_helper/db_optimize/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/dbOptimize/index.html");
        });
        // 数据表优化index

        Route::any('plugs/db_helper/api/get_list', 'app\plugs\dbHelper\controller\DbHelperController@get_list');

        // 数据表控制器路由
        Route::any('plugs/db_helper/db_tables/api/get_list', 'app\plugs\dbHelper\controller\DbTablesController@get_list');
        // 数据表控制器路由

        // 字典控制器路由
        Route::any('plugs/db_helper/db_dict/api/get_list', 'app\plugs\dbHelper\controller\DbDictController@get_list');
        Route::any('plugs/db_helper/db_dict/api/get_tables', 'app\plugs\dbHelper\controller\DbDictController@get_tables');
        // 字典控制器路由
        
        // 数据表优化
        Route::any('plugs/db_helper/db_optimize/api/get_list', 'app\plugs\dbHelper\controller\DbOptimizeController@get_list');
        Route::post('plugs/db_helper/db_optimize/api/perform_optimize', 'app\plugs\dbHelper\controller\DbOptimizeController@perform_optimize');
        // 数据表优化

    }
}