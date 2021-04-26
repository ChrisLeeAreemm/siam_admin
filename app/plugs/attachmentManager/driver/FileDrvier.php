<?php


namespace app\plugs\attachmentManager\driver;


use app\model\UsersModel;
use app\plugs\attachmentManager\model\PlugsAttachmentListModel;
use think\file\UploadedFile;

class FileDrvier extends AbstractDriver
{

    function upload(UploadedFile $file, UsersModel $user): string
    {
        $savename = \think\facade\Filesystem::disk("attachment")->putFile($user->u_id, $file);

        return "/attachment/".$savename;
    }

    function delete(PlugsAttachmentListModel $upload): bool
    {
        $path = app_path(). "/../public/" . $upload->real_path;

        if (is_file($path))  return unlink($path);
        return true;
    }
}