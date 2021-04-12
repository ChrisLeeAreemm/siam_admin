<?php

namespace app\plugs;


abstract class PlugsBase
{
    abstract public function get_config():PlugsConfig;
    abstract public function install();
    abstract public function remove();
    abstract public function init();

    protected function pre_render_file($path)
    {
        // TODO 文件是否存在判断
        $content = file_get_contents($path);
        // TODO 替换宏
        $map = [
            '__STATIC__' => '',// 获取域名 固定路径
        ];
        return $content;
    }
}