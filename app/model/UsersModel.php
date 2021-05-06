<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\model;
#start
/**
 * bar_users
 * @property mixed      u_id
 * @property mixed      u_password         用户密码
 * @property mixed      u_name             用户名
 * @property mixed      u_account          用户登录名
 * @property mixed      p_u_id             上级u_id
 * @property mixed      role_id
 * @property mixed      u_status           用户状态 -1删除 0禁用 1正常
 * @property mixed      u_level_line       用户层级链
 * @property mixed      last_login_ip      最后登录IP
 * @property mixed      last_login_time    最后登录时间
 * @property mixed      create_time        创建时间
 * @property mixed      update_time        更新时间
 * @property mixed      u_auth
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
        return $this->hasOne(RolesModel::class, 'role_id', 'role_id');
    }

    /**
     * 添加新用户
     * @param array $data
     * @return bool
     */
    public function addUser(array $data)
    {
        //开启事务
        $this->startTrans();

        if (!isset($data['u_account'])) {
            $ConfigsModel      = new ConfigsModel();
            $data['u_account'] = $ConfigsModel->getOneAccount();
        }

        $addRes = $this->save($data);

        # 查询p_u_id的层级链，加上自己的id
        $pUIdWhere = ['u_id' => $data['p_u_id']];
        $pUIdInfo  = $this->where($pUIdWhere)->field('u_level_line,p_u_id')->find();
        $updateRes = $this->save(['u_level_line' => $pUIdInfo['u_level_line'] . '-' . $this->getAttr('u_id')]);

        if (!$addRes || !$updateRes) {
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }
}