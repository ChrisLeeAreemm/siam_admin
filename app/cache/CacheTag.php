<?php

namespace app\cache;

// 所有缓存用到的tag 都需要在这里定义 并 说明key格式
class CacheTag
{
    // 用户信息  =  USER_INFO_[用户id]
    const USER_INFO = "USER_INFO";
    // 插件命令行运行状态 = PLUGS_COMMAND_RUN_STATUS_[插件名]，值为发送的心跳包时间戳
    const PLUGS_COMMAND_RUN_STATUS = "PLUGS_COMMAND_RUN_STATUS";
}