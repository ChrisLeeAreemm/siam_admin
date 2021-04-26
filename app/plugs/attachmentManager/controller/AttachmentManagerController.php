<?php

namespace app\plugs\attachmentManager\controller;


use app\exception\ErrorCode;
use app\plugs\attachmentManager\model\PlugsAttachmentConfigModel;
use app\plugs\PlugsBaseController;

class AttachmentManagerController extends PlugsBaseController
{
    public function get_list()
    {

        return $this->send(ErrorCode::SUCCESS,['list'=>[]],'SUCCESS');
    }

    public function save_config()
    {
        $data = input();

        $list = [];
        foreach ($data as $config_name => $config_value){
            $list[] = [
                'key' => $config_name,
                'value' => $config_value,
            ];
        }
        (new PlugsAttachmentConfigModel)->saveAll($list);

        return $this->send(ErrorCode::SUCCESS, [
        ], 'SUCCESS');
    }

    public function get_config()
    {
        $list = PlugsAttachmentConfigModel::select();

        return $this->send(ErrorCode::SUCCESS, [
            'list' => $list
        ], 'SUCCESS');
    }
}