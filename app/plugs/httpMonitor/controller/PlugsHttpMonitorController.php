<?php

namespace app\plugs\httpMonitor\controller;

use app\exception\ErrorCode;
use app\plugs\httpMonitor\model\PlugsHttpMonitorModel as Model;
use app\plugs\PlugsBaseController;
use think\App;
use think\facade\Db;
use think\Request;
use think\Response;

class PlugsHttpMonitorController extends PlugsBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {
        $page  = input('page', 1);
        $limit = input('limit', 10);
        $where = $this->where_build();
        $result = Model::page($page, $limit)->where($where)->order("id", "DESC")->select();
        $count  = Model::count();

        return $this->send(ErrorCode::SUCCESS,['lists'=>$result,'count'=>$count]);

    }

    public function where_build($where = [])
    {
        $request_sn = input('request_sn');
        if ($request_sn){
            $where['request_sn'] = $request_sn;
        }
        return $where;
    }

    public function get_one()
    {
        $id = input('id');
        $result = Model::find($id);
        if (!$result){
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'获取失败');
        }
            return $this->send(ErrorCode::SUCCESS,['lists'=>$result]);
    }

    /**
    * @return \think\response\Json
    */
    public function add()
    {

        $param = input();

        $start = Model::create($param);

        if (!$start) {
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'新增失败');
         }
            return $this->send(ErrorCode::SUCCESS);
    }

    /**
    * @return \think\response\Json
    */
    public function edit()
    {
        $param = input();
        $start = Model::find($param['id']);
        $res   = $start->save($param);

        if (!$res){
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'编辑失败');
        }
            return $this->send(ErrorCode::SUCCESS);
    }

    /**
    * @return \think\response\Json
    */
    public function delete()
    {
        $id = input('id');

        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS,[],'ok');
    }

    public function view_response()
    {
        $id = input('id');
        $model = Model::find($id);
        /** @var Response $response */
        $response = unserialize($model->response_content);
        return $response->getContent();
    }

    public function resend()
    {
        $id = input('id');
        $model = Model::find($id);
        /** @var Request $request */
        $request = unserialize($model->request_content);
        ob_start();
        $app = new App();
        $http = $app->http;
        $response = $http->run($request);
        $response->send();
        $http->end($response);
        $content =ob_get_contents();
        ob_clean();;

        return $this->send('200', ['result' => $content]);
    }

    public function clear()
    {
        $dump = (new Model)->getName();
        Db::name($dump)->delete(true);
        return $this->send('200');

    }
}