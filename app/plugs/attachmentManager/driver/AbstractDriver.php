<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/24
 * Time: 17:59
 */

namespace app\plugs\attachmentManager\driver;


use app\model\UsersModel;
use app\plugs\attachmentManager\model\PlugsAttachmentListModel;
use think\file\UploadedFile;

abstract class AbstractDriver
{
    abstract function upload(UploadedFile $file, UsersModel $user): string;
    abstract function delete(PlugsAttachmentListModel $upload): bool;
}