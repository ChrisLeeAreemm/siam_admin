<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\model;

use think\Model;

#start
/**
 * bar_auths
 * @property mixed auth_id	权限id
 * @property mixed auth_name	权限名
 * @property mixed auth_rules	路由地址
 * @property mixed auth_type	权限类型 0菜单1按钮
 * @property mixed create_time	创建时间
 * @property mixed update_time	更新时间
 */
 #end
class AuthsModel extends BaseModel
{
    protected $name = 'auths';
    protected $pk   = 'auth_id';
}