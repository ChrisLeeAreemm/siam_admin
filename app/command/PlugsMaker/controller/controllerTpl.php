<?php

namespace app\plugs\—PLUGS—\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class —PLUGS—STUDLY—Controller extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}