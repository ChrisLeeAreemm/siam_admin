<?php


namespace app\common;


use think\Exception;

class FileHelper
{
    /**
     * 使用行数获取文件内容
     * @param $file_path
     * @param $start_line
     * @param $end_line
     * @return string
     * @throws \think\Exception
     */
    public static function get_file_content_by_line($file_path, $start_line, $end_line): string
    {
        if (!is_file($file_path)) throw new Exception("文件不存在: $file_path");

        $lines = @file($file_path);
        $len   = $end_line - $start_line + 1;
        return implode(array_slice($lines, $start_line-1,$len));
    }
}