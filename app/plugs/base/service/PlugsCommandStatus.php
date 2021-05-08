<?php

/**
 * 命令行运行状态
 */

namespace app\plugs\base\service;


use app\cache\CacheTag;
use think\facade\Cache;

class PlugsCommandStatus
{
    public static $command = [];

    public static function register($command_name, $command_title, $command)
    {
        static::$command[] = [
            'command_name' => $command_name,
            'command_title' => $command_title,
            'command' => $command,
        ];
    }

    public static function get_command_list()
    {
        return static::$command;
    }

    public static function ping($command_name)
    {
        $key = CacheTag::PLUGS_COMMAND_RUN_STATUS . '_' . $command_name;
        Cache::tag(CacheTag::PLUGS_COMMAND_RUN_STATUS)->set($key, time());
    }

    public static function stop($command_name)
    {
        $key = CacheTag::PLUGS_COMMAND_RUN_STATUS . '_' . $command_name;
        Cache::delete($key);
    }

    /**
     * 命令行是否运行在线
     * @param $command_name
     * @return bool
     */
    public static function online($command_name)
    {
        $key = CacheTag::PLUGS_COMMAND_RUN_STATUS . '_' . $command_name;
        $ping = Cache::tag(CacheTag::PLUGS_COMMAND_RUN_STATUS)->get($key);
        if (!$ping) return false;
        if ((time() - $ping) <= 10 ) return true;

        return false;
    }
}