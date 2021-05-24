<?php


namespace app\plugs\cms\service;
use app\exception\ErrorCode;
use app\exception\ServiceException;
use app\plugs\cms\model\PlugsCmsArticleCategoryModel;
use app\plugs\cms\model\PlugsCmsArticleModel;
use app\plugs\cms\model\PlugsCmsArticleScriptModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

class CmsArticleService
{
    /**
     * 文章列表
     * @param array $where
     * @param int $page
     * @param int $limit
     * @return array
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws ServiceException
     */
    static public function get_article_list($page=1,$limit=10,$where=[])
    {
        $result = PlugsCmsArticleModel::with(['relevanceuser','relevanceArticleCategory'])->where($where)->page($page, $limit)->order('article_id','DESC')->select();
        if (!$result){
            throw new ServiceException('DB_EXCEPTION',ErrorCode::DB_EXCEPTION);
        }
        $script = PlugsCmsArticleScriptModel::field(['article_script_id','article_script_name'])->select();
        if (!$script){
            throw new ServiceException('DB_EXCEPTION',ErrorCode::DB_EXCEPTION);
        }
        $script_arr = [];
        foreach ($script as $value){
            $script_arr[$value['article_script_id']] = $value['article_script_name'];
        }

        foreach ($result as $key => $value){
            $article_script_arr  = explode(',',$value['article_script_list']);
            $script_name = [];
            foreach ($article_script_arr as $v){
                if (!array_key_exists($v,$script_arr)) continue;
                $script_name[] = '['.$script_arr[$v].']';
            }
            $result[$key]['article_script_name'] = implode(',',$script_name);
            unset($script_name);
            unset($article_script_arr);
        }

        return $result->toArray();

    }

    /**
     * 文章详情
     * @param $article_id
     * @return array|\think\Model|null
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws ServiceException
     */
    static public function get_article_info($article_id)
    {
        $result = PlugsCmsArticleModel::with(['relevanceuser','relevanceArticleCategory'])->find($article_id);
        if (!$result){
            throw new ServiceException('DB_EXCEPTION',ErrorCode::DB_EXCEPTION);
        }
        $result['article_script_list'] = explode(',', $result['article_script_list']);
        return $result;
    }

    /**
     * 获取指定文章的脚本内容
     * @param $article_id
     * @return mixed|string
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     * @throws ServiceException
     */
    static public function get_article_script($article_id)
    {
        $article_script_list = PlugsCmsArticleModel::where('article_id',$article_id)->value('article_script_list');
        if (empty($article_script_list)){
            return $article_script_list;
        }
        $article_script = PlugsCmsArticleScriptModel::whereIn('article_script_id', $article_script_list)->select();
        if (!$article_script){
            throw new ServiceException('DB_EXCEPTION',ErrorCode::DB_EXCEPTION);
        }
        //合并脚本，规则按文档约定
        $script_content = '';
        foreach ($article_script as $value){
            $script_content .= PHP_EOL;
            $script_content .= $value['article_script_content'];
        }

        return $script_content;
    }

    /**
     * 文章分类
     * @return array|\think\Collection
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    static public function get_article_category_list()
    {
        $categories = PlugsCmsArticleCategoryModel::field('article_category_id,article_category_name')->select();
        if (!$categories){
            $categories = [];
        }
        return $categories->toArray();
    }
}