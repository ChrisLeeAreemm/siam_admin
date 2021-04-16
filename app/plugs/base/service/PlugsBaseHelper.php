<?php


namespace app\plugs\base\service;


use app\model\PlugsStatusModel;
use Siam\Component\Di;

class PlugsBaseHelper
{
    /**
     * 某插件是否安装，用于插件安装逻辑  检测依赖插件的安装
     * @param $plugs_name
     * @return bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function isInstall($plugs_name): bool
    {
        $has = PlugsStatusModel::where([
            'plugs_name' => $plugs_name
        ])->find();

        Di::getInstance()->set("plugs_".$plugs_name, $has);

        return !!$has;
    }

    /**
     * 获取插件状态
     * @param $plugs_name
     * @return array|null|\think\Model
     * @throws \Throwable
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public static function getPlugsStatus($plugs_name)
    {
        return Di::getInstance()->get("plugs_".$plugs_name) ?? PlugsStatusModel::where([
                'plugs_name' => $plugs_name
            ])->find();
    }


}