<?php

namespace app\plugs\exceptionLogger\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class ExceptionLoggerController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}