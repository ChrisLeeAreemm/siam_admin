<?php


namespace app\plugs\attachmentManager\model;


use app\model\BaseModel;
use think\Model;

#start
/**
 * bar_plugs_attachment_list
 * @property mixed id	自增ID
 * @property mixed u_id	所属用户id
 * @property mixed file_name	文件名
 * @property mixed file_type	文件类型
 * @property mixed file_size	文件大小
 * @property mixed real_path	真实存储地址
 * @property mixed create_time	创建时间
 */
#end
class PlugsAttachmentListModel extends BaseModel
{
    protected $name = 'plugs_attachment_list';
    protected $pk   = 'id';
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;
}