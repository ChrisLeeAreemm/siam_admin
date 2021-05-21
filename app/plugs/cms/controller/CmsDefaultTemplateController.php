<?php


namespace app\plugs\cms\controller;


use app\plugs\cms\service\CmsArticleService;
use think\facade\View;

class CmsDefaultTemplateController
{
    public function index()
    {
        $category_id = input('category_id','');
        //文章列表
        $article_list = CmsArticleService::get_article_list($category_id);
        //文章分类
        $category_list = CmsArticleService::get_article_category_list();
        View::assign('category_list',$category_list);
        View::assign('article_list',$article_list);
        return View::fetch('/plugs/cms/template/default/index');
    }

    public function article()
    {
        $article_id = input('article_id');
        $article_info = CmsArticleService::get_article_info($article_id);
        //文章列表
        $article_list = CmsArticleService::get_article_list();
        //文章分类
        $category_list = CmsArticleService::get_article_category_list();
        View::assign('category_list', $category_list);
        View::assign('article_list', $article_list);
        View::assign('article_info', $article_info);
        return View::fetch('/plugs/cms/template/default/article');
    }
}