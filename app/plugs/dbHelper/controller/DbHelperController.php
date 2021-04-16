<?php

namespace app\plugs\dbHelper\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class DbHelperController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}