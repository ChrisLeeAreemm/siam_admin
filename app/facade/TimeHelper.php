<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/18
 * Time: 18:07
 */

namespace app\facade;


class TimeHelper
{
    public static function get_now_ms()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}