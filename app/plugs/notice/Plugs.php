<?php

namespace app\plugs\notice;


use app\plugs\base\service\PlugsDatabaseHelper;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use EasySwoole\DDL\Blueprint\Create\Table;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;
use think\facade\Route;

/**
 * 站内信
 * Class Plugs
 * @package app\plugs\notice
 */
class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("notice");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "notice",
                'href'   => "/plugs/notice/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
            [
                'title'  => "send",
                'href'   => "/plugs/notice/send",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
        ]);
        return $config;
    }

    public function install()
    {
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_notice", function (Table $table) {
            $table->setIfNotExists()->setTableComment('通知内容表');          //设置表名称
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);      //设置表字符集
            $table->setTableEngine(Engine::INNODB);                      //设置表引擎
            $table->int('notice_id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('ID');
            $table->varchar('notice_title', 255)->setColumnComment("消息标题");
            $table->text('notice_content')->setColumnComment("消息内容");
            $table->int('notice_sender')->setColumnComment("发送者");
            $table->varchar('notice_receiver',255)->setDefaultValue(0)->setColumnComment("接受者:0-通知所有用户、其他数字通知单用户");
            $table->datetime("create_time")->setColumnComment("创建时间");
            $table->datetime("update_time")->setColumnComment("更新时间");
        }));

        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_notice_read", function (Table $table) {
            $table->setIfNotExists()->setTableComment('通知用户阅读表');          //设置表名称
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);      //设置表字符集
            $table->setTableEngine(Engine::INNODB);                      //设置表引擎
            $table->int('notice_read_id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('ID');
            $table->int('u_id')->setColumnComment("用户ID");
            $table->int('notice_id')->setColumnComment("消息ID");
            $table->datetime("create_time")->setColumnComment("创建时间");
            $table->datetime("update_time")->setColumnComment("更新时间");
        }));

    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        Route::get('plugs/notice/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::get('plugs/notice/send', function () {
            return $this->pre_render_file(__DIR__ . "/view/send.html");
        });
        Route::get('plugs/notice/read', function () {
            return $this->pre_render_file(__DIR__ . "/view/read.html");
        });
        Route::any('plugs/notice/api/get_list', 'app\plugs\notice\controller\NoticeController@get_list');
        Route::any('plugs/notice/api/send_notice', 'app\plugs\notice\controller\NoticeController@send_notice');
        Route::any('plugs/notice/api/read_notice', 'app\plugs\notice\controller\NoticeController@read_notice');
    }
}