<?php


namespace app\handle;

use think\facade\Db;
use Workerman\Lib\Timer;

class Worker
{

    public function onWorkerInit()
    {
        // 数据库心跳
        Timer::add(5 , function(){
            $connect = Db::instance();
            $connect->query("select 1");
        });
    }
}