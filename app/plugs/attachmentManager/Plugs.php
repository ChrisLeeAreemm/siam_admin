<?php

namespace app\plugs\attachmentManager;


use app\plugs\errorCode\controller\AttachmentManagerController;
use app\plugs\PlugsBase;
use app\plugs\PlugsConfig;
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
        // TODO 附件管理配置表
        // TODO 附件列表
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
        Route::any('plugs/attachment_manager/api/get_list', 'app\plugs\attachmentManager\controller\AttachmentManagerController@get_list');

        // TODO 这里还要注入一个admin page和接口  添加到auth菜单 给用户使用（上传 删除自己的图片）
    }
}