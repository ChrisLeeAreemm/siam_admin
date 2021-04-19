<?php

namespace app\plugs;


use app\exception\ErrorCode;
use think\Exception;

abstract class PlugsBase
{
    abstract public function get_config(): PlugsConfig;

    abstract public function install();

    abstract public function remove();

    abstract public function init();

    protected function pre_render_file($path)
    {
        // 文件是否存在判断
        if (!is_file($path)){
            throw new Exception('文件不存在',ErrorCode::FILE_NOT_EXIST);
        }
        $content = file_get_contents($path);
        if (!$content) {
            throw new Exception('文件读取错误',ErrorCode::FILE_READ_FAIL);
        }
        //  替换宏
        $map = [
            '__STATIC__' => request()->domain() . '/static',// 获取域名 固定路径
            '__ADMIN__'  => request()->domain() . '/admin',// 获取域名 固定路径
        ];
        foreach ($map as $key => $value) {
            $content =  str_replace($key, $value, $content);
        }
        return $content;
    }
}