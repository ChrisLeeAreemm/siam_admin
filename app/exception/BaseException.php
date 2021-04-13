<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/13
 * Time: 20:17
 */

namespace app\exception;


use think\Exception;

class BaseException extends Exception
{
    protected $return = [];
    public function set_return($data)
    {
        $this->return = [];
    }

    public function get_return()
    {
        return $this->return;
    }
}