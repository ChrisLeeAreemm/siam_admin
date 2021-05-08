<?php
/**
 * Created by CurdPlugs
 */

namespace app\plugs\notice\model;

use app\model\BaseModel;
use app\model\UsersModel;


#start
/**
 * bar_plugs_notice
 * @property mixed notice_id	ID
 * @property mixed notice_title	消息标题
 * @property mixed notice_content	消息内容
 * @property mixed notice_sender	发送者
 * @property mixed notice_receiver	接受者:0-通知所有用户、其他数字通知单用户
 * @property mixed notice_type		消息类型 0-普通 1-强制
 * @property mixed create_time	创建时间
 * @property mixed update_time	更新时间
 */
#end
class PlugsNoticeModel extends BaseModel
{
    protected $name = 'plugs_notice';
    protected $pk   = 'notice_id';

    const NOTICE_TYPE_NORMAL  = 0;
    const NOTICE_TYPE_FORCE   = 1;
    const NOTICE_RECEIVER_ALL = 0;
    /**
     * @relevance 关联方法标识
     * @return mixed
     */
    public function users()
    {
        return $this->hasOne(UsersModel::class, 'u_id', 'notice_sender');
    }

}