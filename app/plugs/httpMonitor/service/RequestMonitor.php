<?php
/**
 * 请求记录监控者
 * User: Administrator
 * Date: 2021/4/16
 * Time: 20:07
 */

namespace app\plugs\httpMonitor\service;


use app\facade\SiamApp;
use app\model\PlugsStatusModel;
use app\plugs\httpMonitor\model\PlugsHttpMonitorModel;
use think\facade\Db;
use think\Request;

class RequestMonitor
{
    public static function run(Request $request):?int
    {
        // 不监听plugs的请求
        if (SiamApp::getInstance()->getModule() === 'plugs') return null;

        $model = new PlugsHttpMonitorModel;
        $model->path = $request->pathinfo();
        $model->request_content = serialize($request);
        $model->response_content = null;
        $model->save();
        return $model->id;

    }
}