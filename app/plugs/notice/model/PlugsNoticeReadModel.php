<?php
/**
 * Created by CurdPlugs
 */

namespace app\plugs\notice\model;

use app\model\BaseModel;

#start
/**
 * bar_plugs_notice_read
 * @property mixed notice_read_id	ID
 * @property mixed u_id	用户ID
 * @property mixed notice_id	消息ID
 * @property mixed create_time	创建时间
 * @property mixed update_time	更新时间
 */
#end
class PlugsNoticeReadModel extends BaseModel
{
    protected $name = 'plugs_notice_read';
    protected $pk   = 'notice_use_id';
}