<?php
/**
 * Created by CurdPlugs
 */

namespace app\model;

use app\exception\ErrorCode;
use think\Exception;

#start
/**
 * bar_configs
 * @property mixed config_id ID
 * @property mixed config_name 配置名
 * @property mixed config_value 配置值
 * @property mixed u_id
 * @property mixed create_time
 * @property mixed update_time
 */
#end
class ConfigsModel extends BaseModel
{
    protected $name = 'configs';
    protected $pk   = 'config_id';

    /**
     * 获取新的用户账户
     * @return string
     */
    public function getOneAccount(): string
    {

        try {
            $info         = $this->where('config_name', 'next_user_id')->find();
            $user_next_id = $info['config_value'] + 1;
            $this->where('config_name', 'next_user_id')->save(['config_value' => $user_next_id]);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage(), ErrorCode::DB_EXCEPTION);
        }

        // 获取完还要随机拼接一个 防止并发
        $info['config_value'] .= rand(0, 9);

        return $info['config_value'];
    }
}