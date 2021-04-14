<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\model;

use think\Model;

#start
/**
 * bar_roles
 * @property mixed role_id	角色id
 * @property mixed role_name	角色名
 * @property mixed role_auth	角色权限
 * @property mixed role_status	角色状态 0正常1禁用
 * @property mixed level	角色级别 越小权限越高
 * @property mixed create_time	创建时间
 * @property mixed update_time	更新时间
 */
 #end
class RolesModel extends BaseModel
{
    protected $name = 'roles';
    protected $pk   = 'role_id';
}