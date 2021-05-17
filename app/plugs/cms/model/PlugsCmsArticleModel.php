<?php
/**
 * Created by CurdPlugs
 */

namespace app\plugs\cms\model;

use app\model\BaseModel;
use app\model\UsersModel;

#start
/**
 * bar_plugs_cms_article
 * @property mixed article_id	ID
 * @property mixed article_title	文章标题
 * @property mixed article_content	文章内容
 * @property mixed article_category_id	文章分类ID
 * @property mixed article_script_list	文章脚本ids
 * @property mixed article_author_id	作者ID
 * @property mixed article_status	文章状态 0:草稿 1：发布
 * @property mixed update_time	更新时间
 * @property mixed create_time	创建时间
 * @property PlugsCmsArticleCategoryModel relevance_article_category
 * @property PlugsCmsArticleScriptModel   relevance_article_script
 * @property UsersModel                   relevance_user
 */
#end
class PlugsCmsArticleModel extends BaseModel
{
    protected $name = 'plugs_cms_article';
    protected $pk   = 'article_id';

    /**
     * 文章分类关联
     * @relevance 关联方法标识
     * @return mixed
     */
    public function relevanceArticleCategory()
    {
        return $this->belongsTo(PlugsCmsArticleCategoryModel::class,'article_category_id','article_category_id')->bind(['article_category_name']);
    }

    /**
     * 用户关联
     * @relevance 关联方法标识
     * @return mixed
     */
    public function relevanceUser()
    {
        return $this->belongsTo(UsersModel::class,'article_author_id','u_id')->bind(['u_name']);
    }
}