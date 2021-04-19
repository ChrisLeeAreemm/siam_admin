<?php

namespace app\plugs\exceptionLogger;


use app\plugs\base\service\PlugsDatabaseHelper;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use EasySwoole\DDL\Blueprint\Create\Table;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;
use think\facade\Route;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("exceptionLogger");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "exceptionLogger",
                'href'   => "/plugs/exception_logger/index",
                'icon'   => "fa fa-frown-o",
                'target' => '_self',
            ],
        ]);
        return $config;
    }

    public function install()
    {
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_exception_logger", function (Table $table) {
            $table->setIfNotExists()->setTableComment('exception记录表');          //设置表名称
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);     //设置表字符集
            $table->setTableEngine(Engine::INNODB);                     //设置表引擎
            $table->int('id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('自增ID');
            $table->varchar('exception_class', 255)->setColumnComment("异常类名(异常类型)");
            $table->int('exception_date')->setColumnComment("异常日期");
            $table->longtext('exception_data')->setColumnComment("异常内容");
            $table->datetime("create_time")->setColumnComment("创建时间");
        }));
    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/exception_logger/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::any('plugs/exception_logger/api/get_list', 'app\plugs\exceptionLogger\controller\ExceptionLoggerController@get_list');
    }
}