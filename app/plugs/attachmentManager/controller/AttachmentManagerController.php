<?php

namespace app\plugs\attachmentManager\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

class AttachmentManagerController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }
}