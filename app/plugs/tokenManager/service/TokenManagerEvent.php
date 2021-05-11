<?php


namespace app\plugs\tokenManager\service;


use app\event\EventTag;
use app\exception\ErrorCode;
use app\facade\SQLiteFacade;
use think\Exception;
use think\Event;
use think\facade\Cache;


class TokenManagerEvent
{
    // TOKEN_MANAGER_[token]   值为注册时间
    const TABLE_NAME = 'token_manager';

    protected $builder;

    public function __construct()
    {
        $sqlite        = SQLiteFacade::connect();
        $this->builder = SQLiteFacade::builder($sqlite)->table(self::TABLE_NAME);
    }
    // token生成规则 暂时用不到
    public function create_token()
    {

    }
    // 销毁token
    public function destory_token($token)
    {
        $this->builder->where('token', $token)->delete();
    }

    // 注册token
    public function register_token($params)
    {
        //单点登录,删除之前的登录Token
        if (Cache::get('single_sign') == true){
            $this->builder->where('user_identify', $params['u_id'])->delete();
        }
        $this->builder->table(self::TABLE_NAME)->insert([
            'user_identify' => $params['u_id'],
            'token'         => $params['token'],
            'create_time'   => date('Y-m-d H:i:s')
        ]);
    }

    // 验证token
    public function auth_token($token)
    {
        $has = $this->builder->where('token',$token)->find();
        if (!$has){
            throw new Exception('AUTH_TOKEN_ERROR',ErrorCode::AUTH_TOKEN_ERROR);
        }
    }

    public function subscribe(Event $event)
    {
        $event->listen(EventTag::CREATE_TOKEN, [$this, 'create_token']);
        $event->listen(EventTag::DESTORY_TOKEN, [$this, 'destory_token']);
        $event->listen(EventTag::REGISTER_TOKEN, [$this, 'register_token']);
        $event->listen(EventTag::AUTH_TOKEN, [$this, 'auth_token']);
    }

}