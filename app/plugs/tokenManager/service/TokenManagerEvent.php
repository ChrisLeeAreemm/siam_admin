<?php


namespace app\plugs\tokenManager\service;


use app\event\EventTag;
use think\facade\Event;

// TODO 不用redis  用sqlite   封装在 app\facade\SQLiteFacade 中
class TokenManagerEvent
{
    // TOKEN_MANAGER_[token]   值为注册时间
    const TABLE_NAME = 'token_manager';

    // token生成规则 暂时用不到
    public function create_token()
    {

    }
    // TODO 销毁token
    public function destory_token($token)
    {
        // 删除redis
    }
    // TODO 注册token
    public function register_token($token)
    {
        // 添加到redis
    }
    // TODO 验证token
    public function auth_token($token)
    {
        // 是否在redis中
    }

    public function subscribe(Event $event)
    {
        $event->listen(EventTag::CREATE_TOKEN, [$this,'create_token']);
        $event->listen(EventTag::DESTORY_TOKEN, [$this,'destory_token']);
        $event->listen(EventTag::REGISTER_TOKEN, [$this,'register_token']);
        $event->listen(EventTag::AUTH_TOKEN, [$this,'auth_token']);
    }

}