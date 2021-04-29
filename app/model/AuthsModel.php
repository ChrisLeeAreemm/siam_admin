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
     * 常量权限类型 0菜单1按钮
     */
    const MENU = 0;
    const BUTTON = 1;

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
        ];
        if ($u_id == 1){
            $auth_type_default = [
                static::MENU,
                static::BUTTON,
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

        $map[] = ['auth_type', 'in',$auth_type];
        if ($u_id !== 1){
            $map = [
                ['auth_id', 'in', $u_auth_ids],
                ['auth_type', 'in',$auth_type],
            ];
        }

        $list = $this->getAuth($map);


        return $list;
    }

    protected function getAuth($map)
    {
        // 这里做了更改 只要是 管理员 都能查看员工权限并修改
        $list = $this::where($map)->select();
        if (!empty($list)) {
            return $list;
        }
        return [];
    }
}