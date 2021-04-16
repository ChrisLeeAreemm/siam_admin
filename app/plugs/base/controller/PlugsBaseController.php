<?php

namespace app\plugs\base\controller;

use app\exception\ErrorCode;
use app\model\PlugsStatusModel;
use app\plugs\PlugsBaseController as BaseController;
use think\Facade\Db;
use think\helper\Str;
use think\response\Json;

class PlugsBaseController extends BaseController
{
    /**
     * 插件安装
     * @return Json
     */
    public function install(): Json
    {
        //安装,获取插件名 , 执行插件的 install , 已安装的不执行
        $this->validate(['plugs_name' => 'require'], $this->request->param());
        $plugsName = $this->request->param('plugs_name');

        $dir       = app_path() . 'plugs\\';
        $arr       = scandir($dir);
        $namespace = '\app\plugs\\';

        //遍历插件
        foreach ($arr as $key => $dirName) {
            if (Str::contains($dirName, '.') == false && is_dir($dir . $dirName)) {
                $Plugs      = $namespace . $dirName . '\Plugs';
                $PlugsModel = new $Plugs();

                //判断插件名
                $name = $PlugsModel->get_config()->getName();
                if ($name !== $plugsName) {
                    continue;
                }

                //检查是否安装
                $plugs_has = PlugsStatusModel::find($name);

                if ($plugs_has) {
                    return $this->send(ErrorCode::SUCCESS, [], '插件已经安装过');
                }

                //执行安装方法
                $install = $PlugsModel->install();
                if ($install === false) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, [], '插件安装失败');
                }

                //写入安装记录
                $data         = [
                    'plugs_name'    => $name,
                    'plugs_status ' => 0,
                    'create_time'   => date('Y-m-d H:i:s')
                ];

                $save_install = PlugsStatusModel::create($data);

                if (!$save_install) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, '', '插件安装记录写入失败，请重试');
                }
                return $this->send(ErrorCode::SUCCESS, [], '插件安装成功');

            }
        }
        return $this->send(ErrorCode::THIRD_PART_ERROR, [], '插件不存在');


    }

    /**
     * 插件状态控制
     * @return Json
     */
    public function status(): Json
    {

        $this->validate(['status' => 'require', 'plugs_name' => 'require'], $this->request->param());
        $status    = $this->request->param('status');
        $plugsName = $this->request->param('plugs_name');
        //获取插件状态

        //base无法控制
        if ($plugsName === 'base') {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '参数无效');
        }

        //判断状态
        $plugsObj = PlugsStatusModel::find($plugsName);
        if (!$plugsObj){
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '插件不存在');
        }


        //执行开启操作
        if ($status == 'on') {
            if ($plugsObj['plugs_status'] != PlugsStatusModel::PLUGS_STATUS_ON) {
                $save = PlugsStatusModel::update(['plugs_status'=>PlugsStatusModel::PLUGS_STATUS_ON],['plugs_name'=>$plugsName]);
                if (!$save) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, [], '开启失败');
                }
            }
            return $this->send(ErrorCode::SUCCESS, [], '插件开启成功');
        }

        //执行关闭操作
        if ($status == 'off') {
            if ($plugsObj['plugs_status'] != PlugsStatusModel::PLUGS_STATUS_OFF) {
                $save = PlugsStatusModel::update(['plugs_status'=>PlugsStatusModel::PLUGS_STATUS_OFF],['plugs_name'=>$plugsName]);
                if (!$save) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, [], '开启失败');
                }
            }
            return $this->send(ErrorCode::SUCCESS, [], '插件关闭成功');
        }

        return $this->send(ErrorCode::PARAM_FORMAT_ERROR, [], '参数值错误');

    }

    function auth()
    {
        // TODO: Implement auth() method.
    }
}