<?php

namespace app\plugs;

use app\facade\SiamApp;
use app\model\PlugsStatusModel;
use Siam\Component\Di;
use think\Exception;
use think\facade\Db;
use think\helper\Str;

class PlugsInitEvent
{
    /**
     * 插件系统初始化
     * @param null $module
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function app_init($module = null): bool
    {
        // 不开启插件模块
        $siam_plugs = env("app.siam_plugs");
        if (!$siam_plugs) return false;

        //检查是否在start文件，是否有安装记录
        if ($module === null){
            $path_info = request()->pathinfo();
            $path_info = explode('/', $path_info);
            SiamApp::getInstance()->setModule($path_info[0]);
            $module = $path_info[0];
        }

        $need_init_plugs_list = static::get_need_init_plugs($module);

        $installed_plugs = PlugsStatusModel::select()->toArray();
        $installed_plugs = array_column($installed_plugs, null, 'plugs_name');

        /** @var \app\plugs\base\Plugs $plugs */
        foreach ($need_init_plugs_list as $plugs)
        {
            //检查安装状态
            if (!isset($installed_plugs[$plugs->get_config()->getName()])) {
                continue;
            }
            $plugs_install_status = $installed_plugs[$plugs->get_config()->getName()];
            //检查启动状态
            if ($plugs_install_status['plugs_status'] != PlugsStatusModel::PLUGS_STATUS_ON) {
                continue;
            }
            //执行init
            $plugs->init();
        }
        return true;
    }

    /**
     * 获取符合当前运行module的插件列表
     * @param $now_module
     * @return array
     */
    public static function get_need_init_plugs($now_module): array
    {
        //遍历plugs目录,执行已经安装过的plugs init
        $arr = scandir(__DIR__);
        $need_init_plugs_list = [];
        foreach ($arr as $dirName) {
            //插件根目录
            $path = __DIR__ . DIRECTORY_SEPARATOR . $dirName;
            if (Str::contains($dirName, '.') == true|| !is_dir($path)){
                continue;
            }

            $PlugsClass = __NAMESPACE__ . '\\' . $dirName . '\Plugs';
            $plugs      = new $PlugsClass();
            //对应模块启动
            $module = $plugs->get_config()->getHandleModule();
            if (!in_array($now_module, $module)) {
                continue;
            }
            $need_init_plugs_list[] = $plugs;
        }
        return $need_init_plugs_list;
    }

}