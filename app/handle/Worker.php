<?php


namespace app\handle;

use think\facade\Db;
use Workerman\Lib\Timer;

class Worker
{

    public function onWorkerInit()
    {
        // 取消ob 直接输出到浏览器
        Timer::add(1, function(){
            ob_end_flush();
        },[], false);

        // 数据库心跳
        Timer::add(1 , function(){
            /** @var \think\db\PDOConnection $connect */
            $connect = Db::instance();
            $connect->query("select 1");
            // var_dump('ping');
            // var_dump($connect->getPdo());// 看对象编号 一直是同一个 所以一个进程只有一个数据库
        });
    }
}