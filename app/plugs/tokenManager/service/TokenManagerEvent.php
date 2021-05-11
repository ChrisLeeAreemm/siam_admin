<?php


namespace app\plugs\tokenManager\service;


use app\event\EventTag;
use app\exception\ErrorCode;
use app\facade\SQLiteFacade;
use think\Exception;
use think\Event;

// TODO 不用redis  用sqlite   封装在 app\facade\SQLiteFacade 中
class TokenManagerEvent
{
    // TOKEN_MANAGER_[token]   值为注册时间
    const TABLE_NAME = 'token_manager';

    protected $builder;

    public function __construct()
    {
        $sqlite        = SQLiteFacade::connect();
        $this->builder = SQLiteFacade::builder($sqlite);
    }
    // token生成规则 暂时用不到
    public function create_token()
    {

    }
    // 销毁token
    public function destory_token($token)
    {
        $this->builder->table(self::TABLE_NAME)->where('token', $token)->delete();
    }

    // 注册token
    public function register_token($token)
    {
        $this->builder->table(self::TABLE_NAME)->insert([
            'token'       => $token,
            'create_time' => time()
        ]);
    }

    // 验证token
    public function auth_token($token)
    {
        $has = $this->builder->table(self::TABLE_NAME)->where('token',$token)->find();
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