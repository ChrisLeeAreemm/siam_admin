<?php


namespace app\handle;


use think\worker\Server;

class Worker extends Server
{
    protected $socket = 'http://0.0.0.0:8443';

    public function onWorkerStart()
    {
        // 定时检测cron
        
    }
}