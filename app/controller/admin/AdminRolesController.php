<?php

namespace app\controller\admin;

use app\exception\ErrorCode;
use app\model\RolesModel as Model;

class AdminRolesController extends AdminBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {

        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = Model::paginate(['page' => $page, 'list_rows' => $limit,])->toArray();
        $lists  = $result['data'];
        $count  = $result['total'];
        return $this->send(ErrorCode::SUCCESS,['lists'=>$lists,'count'=>$count]);


    }

    public function get_one()
    {
        $id = input('role_id');
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
        $start = Model::find($param['role_id']);
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
        $id = input('role_id');

        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS,[],'ok');

    }
}