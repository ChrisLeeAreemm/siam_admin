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
use think\Response;

class SceneResponseMonitor
{
    public static function run(Response $response, $id)
    {
        $model = PlugsHttpMonitorModel::find($id);
        $model->save([
            'response_content' => serialize($response),
            'run_time'         => TimeHelper::get_now_ms() - $model->create_time
        ]);
    }
}