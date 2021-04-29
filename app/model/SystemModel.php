<?php
/**
 * Created by CurdPlugs
 */

namespace app\model;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\Model;

#start
/**
 * bar_system
 * @property mixed id
 * @property mixed user_next_id         下一个用户id
 * @property mixed auth_order           权限排序
 * @property mixed staffs_auth_order    员工权限排序
 */
#end
class SystemModel extends BaseModel
{
    protected $name = 'system';
    protected $pk = 'id';

    /**
     * 获取新的用户账户
     * @return string
     */
    public function getOneAccount(): string
    {

        try {
            $info = $this->field('user_next_id')->where(['id' => 1])->find();
        } catch (DataNotFoundException $e) {
        } catch (ModelNotFoundException $e) {
        } catch (DbException $e) {
        }

        // +1
        try {
            $user_next_id = $info['user_next_id'] + 1;
            $this->where(['id' => 1])->save(['user_next_id' => $user_next_id]);
        } catch (Exception $e) {
        }

        // 获取完还要随机拼接一个 防止并发
        $info['user_next_id'] .= rand(0, 9);

        return $info['user_next_id'];
    }
}