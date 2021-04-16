<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\plugs\httpMonitor\model;

use app\model\BaseModel;
use think\Model;

#start
/**
 * bar_plugs_http_monitor
 * @property mixed id	自增ID
 * @property mixed path	请求地址
 * @property mixed request_content	请求内容序列化
 * @property mixed response_content	响应内容序列化
 * @property mixed run_time	执行耗时
 * @property mixed create_time	请求时间
 */
 #end
class PlugsHttpMonitorModel extends BaseModel
{
    protected $name = 'plugs_http_monitor';
    protected $pk   = 'id';

    protected $autoWriteTimestamp='datetime';
    protected $updateTime = false;
}