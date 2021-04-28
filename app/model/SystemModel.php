<?php
/**
 * Created by CurdPlugs
 */

namespace app\model;

use think\Model;

#start
/**
 * bar_system
 * @property mixed id	
 * @property mixed user_next_id	下一个用户id
 * @property mixed auth_order	权限排序
 * @property mixed staffs_auth_order	员工权限排序
 */
 #end
class SystemModel extends BaseModel
{
    protected $name = 'system';
    protected $pk   = 'id';
}