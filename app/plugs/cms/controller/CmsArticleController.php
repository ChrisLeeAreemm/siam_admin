<?php

namespace app\plugs\cms\controller;


use app\exception\ErrorCode;
use app\plugs\cms\model\PlugsCmsArticleScriptModel;
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
        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = Model::with(['relevanceuser','relevanceArticleCategory'])->page($page, $limit)->order('article_id','DESC')->select();
        $script = PlugsCmsArticleScriptModel::field(['article_script_id','article_script_name'])->select();
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

        $count  = Model::count();
        return $this->send(ErrorCode::SUCCESS,['lists'=>$result,'count'=>$count],'成功');

    }

    public function get_one()
    {
        $id = input('article_id');
        $result = Model::find($id);
        $result['article_script_list'] = explode(',', $result['article_script_list']);
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