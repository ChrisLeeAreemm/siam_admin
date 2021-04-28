<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\model;

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
    /**
     * 常量权限类型 0菜单1按钮2后台特有菜单3后台特有按钮4员工特有菜单5员工特有按钮
     */
    const MENU = 0;
    const BUTTON = 1;
    const ADMIN_MENU = 2;
    const ADMIN_BUTTON = 3;
    const STAFFS_MENU = 4;
    const STAFFS_BUTTON = 5;

    /**
     * 获取user个人权限
     * @param      $u_id
     * @param null $auth_type
     * @return array
     */
    function get_admin_auths_by_u_id($u_id, $auth_type = null)
    {

        $auth_type_default = [
            static::MENU,
            static::BUTTON,
            static::ADMIN_MENU,
            static::ADMIN_BUTTON,
        ];
        if ($u_id == 1){
            $auth_type_default = [
                static::MENU,
                static::BUTTON,
                static::ADMIN_MENU,
                static::ADMIN_BUTTON,
                static::STAFFS_MENU,
                static::STAFFS_BUTTON,
            ];
        }

        if ($auth_type !== null){
            $new_auth_type = [];
            foreach ($auth_type as $auth_type_key => $auth_type_value) {
                if (in_array($auth_type_value, $auth_type_default)){
                    $new_auth_type[] = $auth_type_value;
                }
            }
            $auth_type = $new_auth_type;
        }else{
            $auth_type = $auth_type_default;
        }

        $u_auth_ids = explode(',', UsersModel::find(['u_id' => $u_id])->u_auth);
        $where = [
            'auth_type' => ['IN', implode(',',$auth_type)],
            'auth_id'   => ['IN', $u_auth_ids],
        ];

        if ($u_id == 1){
            unset($where['auth_id']);
        }

        $list = $this->getAuth($where);

        return $list;
    }

    protected function getAuth($where)
    {
        // 这里做了更改 只要是管理员 都能查看员工权限并修改 后期有需要再更改
        // 目前添加了判断前后台特有权限
        $list = $this::where($where)->select()->toArray();
        if (!empty($list)) {
            return $list;
        }
        return [];
    }
}