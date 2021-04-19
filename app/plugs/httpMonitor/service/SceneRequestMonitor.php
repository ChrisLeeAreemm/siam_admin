<?php
/**
 * 场景请求监控
 * User: Administrator
 * Date: 2021/4/16
 * Time: 20:07
 */

namespace app\plugs\httpMonitor\service;


use app\facade\SiamApp;
use app\facade\TimeHelper;
use app\plugs\httpMonitor\model\PlugsHttpMonitorModel;
use think\Model;
use think\Request;

class SceneRequestMonitor
{
    public static function run($request_sn,$request = null)
    {
        // 不监听plugs的请求
        if (SiamApp::getInstance()->getModule() === 'plugs') return null;
        $model = new PlugsHttpMonitorModel;
        $model->path = $request->pathinfo()??'';
        $model->request_sn = $request_sn;
        $model->request_content = serialize(input());
        $model->response_content = null;
        $model->create_time      = TimeHelper::get_now_ms();
        $model->save();
        return $model->id;
    }
}