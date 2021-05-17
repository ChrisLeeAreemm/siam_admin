<?php
/**
 * Created by CurdPlugs
 */

namespace app\plugs\cms\model;

use app\model\BaseModel;

#start
/**
 * bar_plugs_cms_article_category
 * @property mixed article_category_id	ID
 * @property mixed article_category_name	文章分类名
 * @property mixed update_time	更新时间
 * @property mixed create_time	创建时间
 */
#end
class PlugsCmsArticleCategoryModel extends BaseModel
{
    protected $name = 'plugs_cms_article_category';
    protected $pk   = 'article_category_id';
}