<?php


namespace app\common;


use app\exception\AuthException;
use app\exception\ErrorCode;
use app\model\AuthsModel;
use app\model\RolesModel;
use app\model\UsersModel;

class UserAuthHelper
{
    /**
     * 获取权限列表：合并角色权限 + 个人独立权限
     * @param \app\model\UsersModel $user
     * @return array
     */
    public static function get_list_by_user(UsersModel $user)
    {
        $auth_id_list = [];

        $role_list = RolesModel::select(explode(',',$user->role_id));
        /** @var RolesModel $role */
        foreach ($role_list as $role){
            $auth_id_list = array_merge($auth_id_list,explode(',', $role->role_auth));
        }

        $auth_id_list = array_merge($auth_id_list, explode(',', $user->u_auth));

        $auth_id_list = array_unique($auth_id_list);

        return AuthsModel::select($auth_id_list)->toArray();
    }

    /**
     * 验证权限
     * @param \app\model\UsersModel $user
     * @param                       $auth_rules
     * @param bool                  $throw_exception
     * @return bool
     */
    public static function vif_auth_by_user(UsersModel $user, $auth_rules, $throw_exception = true)
    {
        $list = array_column(static::get_list_by_user($user), 'auth_rules');

        $result = in_array($auth_rules, $list);
        if (!$result && $throw_exception){
            throw new AuthException("用户没有权限".$auth_rules, ErrorCode::AUTH_NONE_NODE);
        }
        return $result;
    }
}