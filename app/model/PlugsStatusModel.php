<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\model;

use think\Model;

#start
/**
 * bar_plugs_status
 * @property mixed plugs_name
 * @property mixed plugs_status    插件开启状态 0停用 | 1启用
 * @property mixed create_time
 */
#end
class PlugsStatusModel extends BaseModel
{
    protected $name = 'plugs_status';
    protected $pk   = 'plugs_name';
    //插件开启状态
    const PLUGS_STATUS_ON  = 1;
    const PLUGS_STATUS_OFF = 0;
}