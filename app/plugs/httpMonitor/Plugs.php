<?php

namespace app\plugs\httpMonitor;


use app\plugs\base\service\PlugsDatabaseHelper;
use app\plugs\errorCode\controller\HttpMonitorController;
use app\plugs\httpMonitor\service\RequestMonitor;
use app\plugs\httpMonitor\service\ResponseMonitor;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use EasySwoole\DDL\Blueprint\Create\Table;
use EasySwoole\DDL\DDLBuilder;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;
use think\facade\Db;
use think\facade\Event;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("httpMonitor");
        $config->setHandleModule(["notify", "plugs"]);
        $config->setMenu([
            [
                'title'  => "httpMonitor",
                'href'   => "/plugs/http_monitor/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
        ]);
        return $config;
    }

    public function install()
    {
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_http_monitor", function(Table $table){
            $table->setIfNotExists()->setTableComment('httpMonitor记录表');          //设置表名称
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);     //设置表字符集
            $table->setTableEngine(Engine::INNODB);                     //设置表引擎
            $table->int('id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('自增ID');
            $table->varchar('path')->setColumnComment("请求地址");
            $table->text('request_content')->setColumnComment("请求内容序列化");
            $table->text('response_content')->setColumnComment("响应内容序列化")->setIsNotNull(false);
            $table->decimal('run_time')->setColumnComment("执行耗时")->setIsNotNull(false);
            $table->datetime("create_time")->setColumnComment("请求时间");
        }));
    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/http_monitor/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::any('plugs/http_monitor/api/get_list', 'app\plugs\httpMonitor\controller\PlugsHttpMonitorController@get_list');

        $id = RequestMonitor::run(request());
        Event::listen('HttpEnd', function($response) use($id) {
            if ($id === null) return ;
            ResponseMonitor::run($response, $id);
        });
    }
}