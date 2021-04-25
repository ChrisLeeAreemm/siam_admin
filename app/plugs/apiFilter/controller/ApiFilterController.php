<?php

namespace app\plugs\apiFilter\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;
use app\plugs\apiFilter\model\PlugsApiFilterSettingModel as Model;

class ApiFilterController extends PlugsBaseController
{
    public function get_list()
    {
        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = Model::page($page, $limit)->order('set_id', 'DESC')->select();
        $count  = Model::count();
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'count' => $count]);
    }

    public function get_one()
    {
        $id     = input('set_id');
        $result = Model::find($id);
        if (!$result) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '获取失败');
        }
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result]);
    }

    /**
     * @return \think\response\Json
     */
    public function add()
    {

        $param                = input();
        $param['create_time'] = date('Y-m-d H:i:s');

        $start = Model::create($param);

        if (!$start) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '新增失败');
        }
        return $this->send(ErrorCode::SUCCESS);
    }

    /**
     * @return \think\response\Json
     */
    public function edit()
    {
        $param                = input();
        $start                = Model::find($param['set_id']);
        $param['update_time'] = date('Y-m-d H:i:s');
        $res                  = $start->save($param);

        if (!$res) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '编辑失败');
        }
        return $this->send(ErrorCode::SUCCESS);
    }

    /**
     * @return \think\response\Json
     */
    public function delete()
    {
        $id = input('set_id');

        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS, [], 'ok');
    }
}