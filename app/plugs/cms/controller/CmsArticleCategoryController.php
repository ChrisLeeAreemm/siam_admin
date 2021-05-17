<?php

namespace app\plugs\cms\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;
use app\plugs\cms\model\PlugsCmsArticleCategoryModel as Model;

class CmsArticleCategoryController extends PlugsBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {

        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = Model::page($page, $limit)->order('article_category_id','DESC')->select();
        $count  = Model::count();
        return $this->send(ErrorCode::SUCCESS,['lists'=>$result,'count'=>$count],'成功');

    }

    /**
     *
     * @return \think\response\Json
     */
    public function get_all()
    {
        try {
            $categories = Model::select();
        } catch (\Exception $e) {
            return $this->send(ErrorCode::DB_EXCEPTION, [], $e->getMessage());
        }
        if (!$categories){
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], 'DB_DATA_DOES_NOT_EXIST');
        }
        return $this->send(ErrorCode::SUCCESS,$categories,'成功');

    }

    public function get_one()
    {
        $id = input('article_category_id');
        $result = Model::find($id);
        if (!$result){
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'获取失败');
        }
            return $this->send(ErrorCode::SUCCESS,['lists'=>$result],'成功');
    }

    /**
    * @return \think\response\Json
    */
    public function add()
    {

        $param = input();
        $param['create_time'] = $param['update_time'] = date('Y-m-d H:i:s');
        $start = Model::create($param);

        if (!$start) {
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'新增失败');
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
        $start = Model::find($param['article_category_id']);
        $res   = $start->save($param);

        if (!$res){
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'编辑失败');
        }
            return $this->send(ErrorCode::SUCCESS,[],'成功');
    }

    /**
    * @return \think\response\Json
    */
    public function delete()
    {
        $id = input('article_category_id');

        $result = Model::destroy($id);
        if (!$result){
                return $this->send(ErrorCode::DB_EXCEPTION, [], '删除失败');
          }
        return $this->send(ErrorCode::SUCCESS,[],'成功');
    }
}