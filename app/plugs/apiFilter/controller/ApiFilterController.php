<?php

namespace app\plugs\apiFilter\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class ApiFilterController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}