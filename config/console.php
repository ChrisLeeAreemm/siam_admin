<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------

use app\plugs\base\service\PlugsCommandStatus;

PlugsCommandStatus::register('cron', "cron执行", 'php think cron');
PlugsCommandStatus::register('plugs', "插件初始包生成", 'php think plugs [插件名]');

return [
    // 指令定义
    'commands' => [
        'cron'  => 'app\command\CronRunnerCommand',
        'plugs' => 'app\command\PlugsMakerCommand',
    ],
];
