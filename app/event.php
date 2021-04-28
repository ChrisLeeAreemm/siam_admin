<?php
// 事件定义文件
return [
    'bind'      => [
        'TokenFilter' => 'app\event\TokenFilter', //自定义Token 限流事件
    ],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => ["app\plugs\PlugsInitEvent::app_init"],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],
    ],

    'subscribe' => [
    ],
];
