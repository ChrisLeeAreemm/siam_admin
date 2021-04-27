<?php
/**
 * Created by CurdPlugs
 */

namespace app\plugs\attachmentManager\model;

use app\model\BaseModel;

#start
/**
 * bar_plugs_attachment_config
 * @property mixed id	自增ID
 * @property mixed key	配置项
 * @property mixed value	配置值
 * @property mixed create_time	创建时间
 */
#end
class PlugsAttachmentConfigModel extends BaseModel
{
    protected $name = 'plugs_attachment_config';
    protected $pk = 'id';
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;
}