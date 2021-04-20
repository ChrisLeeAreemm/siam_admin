<?php


namespace app\handle;


class CronMonitor
{
    public static $task = [];
    /** @var int 1秒检测一次 */
    public static $time = 1;

    public static function run($time = null)
    {
        if ($time) static::$time = $time;
        while(1){
            sleep($time);
        }
    }


}