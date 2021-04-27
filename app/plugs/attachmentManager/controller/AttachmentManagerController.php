<?php

namespace app\plugs\attachmentManager\controller;


use app\exception\ErrorCode;
use app\exception\ServiceException;
use app\plugs\attachmentManager\model\PlugsAttachmentConfigModel;
use app\plugs\attachmentManager\model\PlugsAttachmentListModel;
use app\plugs\attachmentManager\service\AttachmentService;
use app\plugs\PlugsBaseController;

class AttachmentManagerController extends PlugsBaseController
{
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

    public function upload()
    {
        $user = $this->auth();

        $file   = request()->file('file');
        try {
            $upload = AttachmentService::upload($file, $user);
        } catch (ServiceException $e) {
            return $this->send(ErrorCode::FILE_WRITE_FAIL, [
                'exception' => $e->getMessage()
            ], 'FAIL');
        }

        return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
    }

    public function get_list()
    {
        // TODO
    }

    public function delete()
    {
        $this->validate([
            'id' => 'require'
        ]);
        $this->auth();

        $upload = PlugsAttachmentListModel::find(input('id'));
        $this->validate([
            'upload' => 'require'
        ], [
            'upload' => $upload
        ]);

        AttachmentService::delete($upload);

        return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
    }

}