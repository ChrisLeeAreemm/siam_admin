<?php

namespace app\plugs\tokenManager\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class TokenManagerController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}