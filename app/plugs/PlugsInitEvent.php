<?php

namespace app\plugs;

use app\model\PlugsStatusModel;
use think\Exception;
use think\helper\Str;

class PlugsInitEvent
{
    public static function app_init()
    {
        //遍历plugs目录,执行已经安装过的plugs init
        $arr = scandir(__DIR__);

        //检查是否在start文件，是否有安装记录
        $path_info = request()->pathinfo();
        $path_info = explode('/', $path_info);
        foreach ($arr as $dirName) {

            //插件根目录
            $path = __DIR__ . '\\' . $dirName;

            //过滤插件文件夹
            if (Str::contains($path, '.') == false && is_dir($path)) {

                //new Plugs.php 实例
                $PlugsClass = __NAMESPACE__ . '\\' . $dirName . '\Plugs';
                $plugs      = new $PlugsClass();

                //获取插件数据
                $name     = $plugs->get_config()->getName();
                $plugsObj = PlugsStatusModel::find($name);
                //检查安装状态
                if (!$plugsObj) {
                    continue;
                }
                //检查启动状态
                if ($plugsObj->plugs_status != $plugsObj::PLUGS_STATUS_ON) {
                    continue;
                }

                //对应模块启动
                $module = $plugs->get_config()->getHandleModule();
                if (!in_array($path_info[0], $module)) {
                    continue;
                }
                //执行init
                $plugs->init();
            }

        }
    }

}