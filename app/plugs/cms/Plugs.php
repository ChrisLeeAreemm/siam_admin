<?php

namespace app\plugs\cms;

use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\facade\Route;
use app\plugs\base\service\PlugsDatabaseHelper;
use EasySwoole\DDL\Blueprint\Create\Table;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("cms");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "cms",
                'href'   => "/plugs/cms/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
        ]);
        return $config;
    }

    public function install()
    {
        // TODO 文章
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_cms_article", function (Table $table){

        }));

        // TODO 文章分类
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_cms_article_category", function (Table $table){

        }));

        // TODO 文章脚本
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_cms_article_script", function (Table $table){

        }));
    }

    public function remove()
    {

    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/cms/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::any('plugs/cms/api/get_list', 'app\plugs\cms\controller\CmsController@get_list');
    }
}