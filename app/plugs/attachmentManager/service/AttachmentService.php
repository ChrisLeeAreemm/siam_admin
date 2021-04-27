<?php


namespace app\plugs\attachmentManager\service;


use app\exception\ErrorCode;
use app\exception\ServiceException;
use app\model\UsersModel;
use app\plugs\attachmentManager\driver\Factory;
use app\plugs\attachmentManager\model\PlugsAttachmentConfigModel;
use app\plugs\attachmentManager\model\PlugsAttachmentListModel;
use Siam\Component\Di;
use think\file\UploadedFile;
use think\Model;

class AttachmentService
{
    public static function upload(UploadedFile $file, UsersModel $user): PlugsAttachmentListModel
    {
        $config = PlugsAttachmentConfigModel::where([
           ['key', 'IN', ['config_allow','config_file_max', 'config_driver']]
        ])->select();
        $config = array_column($config->toArray(), null, 'key');

        // 检测后缀
        $allow = explode(',', $config['config_allow']['value']);
        if (!in_array($file->getOriginalExtension(), $allow)){
            throw new ServiceException("文件后缀不允许",ErrorCode::FILE_WRITE_FAIL);
        }

        // 检测大小 转为字节
        $max_size = $config['config_file_max']['value'] * 1024 * 1024;
        if ($file->getSize() > $max_size){
            throw new ServiceException("文件大小超限",ErrorCode::FILE_WRITE_FAIL);
        }

        // 根据上传方式 决定上传的驱动
        $save_path = Factory::init($config['config_driver']['value'])->upload($file, $user);

        $upload            = new PlugsAttachmentListModel;
        $upload->u_id      = $user->u_id;
        $upload->file_name = $file->getOriginalName();
        $upload->file_size = $file->getSize();
        $upload->file_type = $file->getOriginalExtension();
        $upload->real_path = $save_path;
        $upload->save();

        return $upload;
    }

    public static function delete(PlugsAttachmentListModel $upload): bool
    {
        // 先根据上传方式 去删除文件 然后再删除这条记录
        // 根据上传方式 决定上传的驱动
        $config = PlugsAttachmentConfigModel::where([
            ['key', '=', 'config_driver']
        ])->find();

        $res = Factory::init($config['value'])->delete($upload);
        if (!$res) return false;

        return $upload->delete();
    }

    public static function render_url(PlugsAttachmentListModel $upload): string
    {
        // 做一个缓存
        $config = Di::getInstance()->get('attachment_config');
        if (!$config){
            $config = PlugsAttachmentConfigModel::where([
                ['key', '=', 'config_host']
            ])->find();
            Di::getInstance()->set('attachment_config', $config);
        }

        if (!$config || !$config->getAttr('value')) {
            $host = request()->domain(false)."/";
        }else{
            $host = $config->getAttr('value');
        }
        return $host . $upload->real_path;
    }
}