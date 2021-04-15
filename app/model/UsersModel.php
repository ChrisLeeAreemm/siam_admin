<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\model;
#start
/**
 * bar_users
 * @property mixed u_id	
 * @property mixed u_password	用户密码
 * @property mixed u_name	用户名
 * @property mixed u_account	用户登录名
 * @property mixed p_u_id	上级u_id
 * @property mixed role_id	
 * @property mixed u_status	用户状态 -1删除 0禁用 1正常
 * @property mixed u_level_line	用户层级链
 * @property mixed last_login_ip	最后登录IP
 * @property mixed last_login_time	最后登录时间
 * @property mixed create_time	创建时间
 * @property mixed update_time	更新时间
 * @property mixed u_auth	
 * @property RolesModel roles	
 */
 #end
class UsersModel extends BaseModel
{
    protected $name = 'users';
    protected $pk   = 'u_id';
    
    /**
     * @relevance 关联方法标识
     * @return mixed
     */
    public function roles()
    {
        return $this->hasOne(RolesModel::class,'role_id','role_id');
    }
}