<?php

namespace app\cache;

// 所有缓存用到的tag 都需要在这里定义 并 说明key格式
class CacheTag
{
    // 用户信息  =  USER_INFO_[用户id]
    const USER_INFO = "USER_INFO";
}