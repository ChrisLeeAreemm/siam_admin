<?php
/**
 * 请求记录监控者
 * User: Administrator
 * Date: 2021/4/16
 * Time: 20:07
 */

namespace app\plugs\httpMonitor\service;


use app\plugs\httpMonitor\model\PlugsHttpMonitorModel;
use think\Response;

class ResponseMonitor
{
    public static function run(Response $response, $id)
    {
        $model = PlugsHttpMonitorModel::find($id);
        $model->save([
            'response' => serialize($response),
            'run_time' => time() - strtotime($model->create_time)
        ]);
    }
}