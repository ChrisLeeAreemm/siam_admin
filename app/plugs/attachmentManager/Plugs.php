<?php

namespace app\plugs\attachmentManager;


use app\plugs\base\service\PlugsDatabaseHelper;
use app\plugs\errorCode\controller\AttachmentManagerController;
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
        $config->setName("attachmentManager");
        $config->setHandleModule(["admin", "plugs"]);// 只有admin模块才会执行初始化
        $config->setMenu([
            [
                'title'  => "attachmentManager",
                'href'   => "/plugs/attachment_manager/index",
                'icon'   => "fa fa-tachometer",
                'target' => '_self',
            ],
        ]);
        return $config;
    }

    public function install()
    {
        // 附件管理配置表
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_attachment_config", function (Table $table) {
            $table->setIfNotExists()->setTableComment('附件配置表');
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);
            $table->setTableEngine(Engine::INNODB);
            $table->varchar('key', 255)->setIsPrimaryKey()->setColumnComment('配置项');
            $table->varchar('value', 255)->setColumnComment("配置值");
            $table->datetime("create_time")->setColumnComment("创建时间");
        }));
        // 附件列表
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_attachment_list", function (Table $table) {
            $table->setIfNotExists()->setTableComment('附件列表');
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);
            $table->setTableEngine(Engine::INNODB);
            $table->int('id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('自增ID');
            $table->int('u_id', 10)->setColumnComment("所属用户id");
            $table->varchar('file_name', 255)->setColumnComment("文件名");
            $table->varchar('file_type', 255)->setColumnComment("文件类型");
            $table->varchar('file_size', 255)->setColumnComment("文件大小");
            $table->varchar('real_path', 255)->setColumnComment("真实存储地址");
            $table->datetime("create_time")->setColumnComment("创建时间");
        }));
    }

    public function remove()
    {
    }

    public function init()
    {
        // 在这里注入路由[api] 等事件
        // 这里是图片插件管理主页   允许上传后缀、允许上传大小、使用驱动（File 阿里云OSS 腾讯云OSS等）
        Route::get('plugs/attachment_manager/index', function () {
            return $this->pre_render_file(__DIR__ . "/view/index.html");
        });
        Route::get('plugs/attachment_manager/manage', function () {
            return $this->pre_render_file(__DIR__ . "/view/manage.html");
        });

        Route::group(function () {
            // 配置编辑相关
            Route::any('plugs/attachment_manager/api/save_config', 'AttachmentManagerController@save_config');
            Route::any('plugs/attachment_manager/api/get_config', 'AttachmentManagerController@get_config');
            // 上传附件、获取附件列表、删除附件  三个接口
            Route::any('plugs/attachment_manager/api/upload', 'AttachmentManagerController@upload');
            Route::any('plugs/attachment_manager/api/get_list', 'AttachmentManagerController@get_list');
            Route::any('plugs/attachment_manager/api/delete', 'AttachmentManagerController@delete');

            // TODO 这里还要注入一个admin page和接口  添加到auth菜单 给用户使用（上传 删除自己的图片）

        })->prefix('app\plugs\attachmentManager\controller\\');


    }
}