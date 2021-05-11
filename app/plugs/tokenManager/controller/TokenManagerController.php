<?php

namespace app\plugs\tokenManager\controller;


use app\exception\ErrorCode;
use app\facade\SQLiteFacade;
use app\model\UsersModel;
use app\plugs\PlugsBaseController;
use app\plugs\tokenManager\service\TokenManagerEvent;
use think\facade\Cache;

class TokenManagerController extends PlugsBaseController
{

    public function get_list()
    {
        $sqlite  = SQLiteFacade::connect();
        $builder = SQLiteFacade::builder($sqlite);
        $page    = input('page', 1);
        $limit   = input('limit', 10);
        $result  = $builder->table(TokenManagerEvent::TABLE_NAME)->page($page, $limit)->order('id', 'desc')->select();
        $user_ids = [];
        foreach ($result as $value){
            $user_ids[] = $value['user_identify'];
        }

        $user = UsersModel::field('u_id,u_name')->whereIn('u_id',array_unique($user_ids))->select();
        unset($user_ids);

        foreach ($user as $value){
            $user_map[$value['u_id']] = $value['u_name'];
        }
        unset($user);

        $result = $result->toArray();
        foreach ($result as $key=>$value){
            if (isset($user_map[$value['user_identify']])){
                $result[$key]['u_name'] = $user_map[$value['user_identify']];
            }
        }
        unset($user_map);
        $count = $builder->table(TokenManagerEvent::TABLE_NAME)->page($page, $limit)->count();
        return $this->send(ErrorCode::SUCCESS,['list'=>$result,'count'=>$count],'SUCCESS');
    }

    /**
     * 下线
     * @return \think\response\Json
     * @throws \app\exception\AuthException
     * @throws \think\db\exception\DbException
     */
    public function outline()
    {
        $this->validate(['token' => 'require'], input());
        $token = input('token');
        (new TokenManagerEvent)->destory_token($token);
        return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
    }

    /**
     * 单点登录
     * @return \think\response\Json
     * @throws \app\exception\AuthException
     */
    public function single_sign()
    {
        $this->validate(['single_sign' => 'require'], input());
        $user_identify = input('single_sign');
        Cache::set('single_sign', $user_identify);
        return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
    }

    public function get_single_sign()
    {
        return $this->send(ErrorCode::SUCCESS, ['type'=>Cache::get('single_sign')], 'SUCCESS');
    }

}