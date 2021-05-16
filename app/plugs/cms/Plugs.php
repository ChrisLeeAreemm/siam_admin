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
        // 文章
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_cms_article", function (Table $table){
            $table->setIfNotExists()->setTableComment('文章列表');          //设置表名称
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);      //设置表字符集
            $table->setTableEngine(Engine::INNODB);                      //设置表引擎
            $table->int('article_id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('ID');
            $table->varchar('article_title', 255)->setColumnComment("文章标题");
            $table->text('article_content')->setColumnComment("文章内容");
            $table->int('article_category_id')->setDefaultValue(0)->setColumnComment("文章分类ID");
            $table->int('article_script_id')->setDefaultValue(0)->setColumnComment("文章脚本ID");
            $table->int('article_author_id')->setColumnComment("作者ID");
            $table->tinyint('article_status',3)->setDefaultValue(0)->setColumnComment("文章状态 0:草稿 1：发布");
            $table->datetime("update_time")->setColumnComment("更新时间");
            $table->datetime("create_time")->setColumnComment("创建时间");
        }));

        // 文章分类
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_cms_article_category", function (Table $table){
            $table->setIfNotExists()->setTableComment('文章分类');          //设置表名称
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);      //设置表字符集
            $table->setTableEngine(Engine::INNODB);                      //设置表引擎
            $table->int('article_category_id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('ID');
            $table->varchar('article_category_name', 255)->setColumnComment("文章分类名");
            $table->datetime("update_time")->setColumnComment("更新时间");
            $table->datetime("create_time")->setColumnComment("创建时间");
        }));

        // 文章脚本
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_cms_article_script", function (Table $table){
            $table->setIfNotExists()->setTableComment('文章脚本');          //设置表名称
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);      //设置表字符集
            $table->setTableEngine(Engine::INNODB);                      //设置表引擎
            $table->int('article_script_id')->setIsUnsigned()->setIsAutoIncrement()->setIsPrimaryKey()->setColumnComment('ID');
            $table->varchar('article_script_name', 255)->setColumnComment("脚本名");
            $table->varchar('article_script_content', 255)->setColumnComment("脚本内容");
            $table->datetime("update_time")->setColumnComment("更新时间");
            $table->datetime("create_time")->setColumnComment("创建时间");
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