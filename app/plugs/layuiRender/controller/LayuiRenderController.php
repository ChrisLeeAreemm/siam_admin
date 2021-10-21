<?php

namespace app\plugs\layuiRender\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class LayuiRenderController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}