<?php

namespace app\plugs\cms\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class CmsController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}