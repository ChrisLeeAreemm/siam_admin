<?php
/**
 * Created by CurdPlugs
 */

namespace app\plugs\apiFilter\model;

use app\model\BaseModel;
#start
/**
 * bar_plugs_api_filter_setting
 * @property mixed set_id	id
 * @property mixed key	配置key
 * @property mixed number	配置数量
 * @property mixed create_time	创建时间
 * @property mixed update_time	更新时间
 */
 #end
class PlugsApiFilterSettingModel extends BaseModel
{
    protected $name = 'plugs_api_filter_setting';
    protected $pk   = 'set_id';
}