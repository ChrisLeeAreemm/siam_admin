<?php
/**
 * Created by CurdPlugs
 */

namespace app\plugs\cms\model;

use app\model\BaseModel;

#start
/**
 * bar_plugs_cms_article_script
 * @property mixed article_script_id	ID
 * @property mixed article_script_name	脚本名
 * @property mixed article_script_content	脚本内容
 * @property mixed update_time	更新时间
 * @property mixed create_time	创建时间
 */
#end
class PlugsCmsArticleScriptModel extends BaseModel
{
    protected $name = 'plugs_cms_article_script';
    protected $pk   = 'article_script_id';
}