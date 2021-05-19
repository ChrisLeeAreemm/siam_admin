<?php

namespace app\plugs\cms\controller;


use app\exception\ErrorCode;
use app\plugs\cms\service\CmsArticleService;
use app\plugs\PlugsBaseController;
use app\plugs\cms\model\PlugsCmsArticleModel as Model;

class CmsArticleController extends PlugsBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {
        $page   = input('page', 1);
        $limit  = input('limit', 10);
        $result = CmsArticleService::get_article_list($page, $limit);
        $count  = Model::page($page, $limit)->count();
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'count' => $count], '成功');
    }

    public function get_one()
    {
        $id = input('article_id');
        $result = CmsArticleService::get_article_info($id);
        if (!$result){
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST,[],'获取失败');
        }
            return $this->send(ErrorCode::SUCCESS,['lists'=>$result],'成功');
    }

    /**
    * @return \think\response\Json
    */
    public function add()
    {

        $param = input();
        $param['article_author_id'] = $this->who->u_id;
        $param['article_script_list'] = implode(',', $param['article_script']);
        $param['create_time'] = $param['update_time'] = date('Y-m-d H:i:s');
        $start = Model::create($param);

        if (!$start) {
            return $this->send(ErrorCode::DB_DATA_ADD_FAILE,[],'新增失败');
         }
            return $this->send(ErrorCode::SUCCESS,[],'成功');
    }

    /**
    * @return \think\response\Json
    */
    public function edit()
    {
        $param = input();
        $param['update_time'] = date('Y-m-d H:i:s');
        $param['article_script_list'] = implode(',', $param['article_script']);
        $start = Model::find($param['article_id']);
        $res   = $start->save($param);

        if (!$res){
            return $this->send(ErrorCode::DB_DATA_UPDATE_FAILE,[],'编辑失败');
        }
            return $this->send(ErrorCode::SUCCESS,[],'成功');
    }

    /**
    * @return \think\response\Json
    */
    public function delete()
    {
        $id = input('article_id');

        $result = Model::destroy($id);
        if (!$result){
                return $this->send(ErrorCode::DB_EXCEPTION, [], '删除失败');
          }
        return $this->send(ErrorCode::SUCCESS,[],'成功');
    }

}