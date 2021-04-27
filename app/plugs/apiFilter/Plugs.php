<?php

namespace app\plugs\apiFilter;

use app\exception\ErrorCode;
use app\plugs\apiFilter\model\PlugsApiFilterSettingModel;
use app\plugs\apiFilter\service\ApiAccessContain;
use app\plugs\base\service\PlugsDatabaseHelper;
use app\facade\SiamApp;
use app\plugs\apiFilter\service\ApiFilterCommand;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
use think\App;
use think\Exception;
use think\facade\Event;
use think\facade\Route;
use EasySwoole\DDL\Blueprint\Create\Table;
use EasySwoole\DDL\Enum\Character;
use EasySwoole\DDL\Enum\Engine;
use Workerman\Events\Ev;

class Plugs extends PlugsBase
{


    public function get_config(): PlugsConfig
    {
        $config = new PlugsConfig();
        $config->setName("apiFilter");
        $config->setHandleModule(["api", "plugs", "cli"]);
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
        PlugsDatabaseHelper::run(PlugsDatabaseHelper::create_ddl("plugs_api_filter_setting", function (Table $table) {
            $table->setIfNotExists()->setTableComment('api限流器配置表');                                      //设置表名称/
            $table->setTableEngine(Engine::MYISAM);                                                      //设置表引擎
            $table->setTableCharset(Character::UTF8MB4_GENERAL_CI);                                      //设置表字符集
            $table->int('set_id', 10)->setColumnComment('id')->setIsAutoIncrement()->setIsPrimaryKey();  //创建user_id设置主键并自动增长
            $table->varchar('key', 50)->setIsNotNull()->setColumnComment('配置key')->setDefaultValue('');
            $table->int('number')->setIsNotNull()->setColumnComment('配置数量')->setDefaultValue(-1);
            $table->datetime('create_time')->setIsNotNull()->setColumnComment('创建时间');
            $table->datetime('update_time')->setIsNotNull()->setColumnComment('更新时间');
        }));
        $time = date('Y-m-d H:i:s');
        PlugsApiFilterSettingModel::create(['key' => 'TOTAL', 'number' => 30, 'create_time' => $time, 'update_time' => $time]);

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
        Route::any('plugs/api_filter/api/add', 'app\plugs\apiFilter\controller\ApiFilterController@add');
        Route::any('plugs/api_filter/api/edit', 'app\plugs\apiFilter\controller\ApiFilterController@edit');
        Route::any('plugs/api_filter/api/get_one', 'app\plugs\apiFilter\controller\ApiFilterController@get_one');
        Route::any('plugs/api_filter/api/delete', 'app\plugs\apiFilter\controller\ApiFilterController@delete');

        // 注入自定义命令行
        $console = SiamApp::getInstance()->getConsole();
        if ($console) {
            $console->addCommand(new ApiFilterCommand(), 'api-filter');
        }
        //TODO 流程重写
        //全局限流
        $set = ApiAccessContain::getInstance()->getNumber(ApiAccessContain::API_FILTER_TOTAL);
        if ($set >= 0) {
            if (ApiAccessContain::getInstance()->getAccess(ApiAccessContain::API_FILTER_TOTAL) > $set) {
                throw new Exception('全局限流',ErrorCode::WAF_IP_FILTER);
            }
        }

        // ip限流
        $ip = request()->ip();
        $set = ApiAccessContain::getInstance()->getNumber($ip);
        if ($set >= 0) {
            print_r($set);
            if (ApiAccessContain::getInstance()->getAccess($ip) > $set) {
                throw new Exception('IP限流',ErrorCode::WAF_IP_FILTER);
            }
        }

        // token限流/分组限流/自定义限流  从Token限流事件中获取Token
        Event::listen('TokenFilter', function ($token) {
            if (isset($token)) {
                $set = ApiAccessContain::getInstance()->getNumber($token);
                if (ApiAccessContain::getInstance()->getAccess($token) > $set) {
                    throw new Exception('Token限流',ErrorCode::WAF_IP_FILTER);
                }
            }
        });


        return true;
    }
}