<?php

namespace app\controller\admin;

use app\exception\ErrorCode;
use app\model\UsersModel as Model;

class AdminUsersController extends AdminBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {

        $page  = input('page', 1);
        $limit = input('limit', 10);
    
        $result = Model::page($page, $limit)->order('u_id','DESC')->select();
        $count  = Model::count();
        return $this->send(ErrorCode::SUCCESS,['lists'=>$result,'count'=>$count]);


    }

    public function get_one()
    {
        $id = input('u_id');
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
        $start = Model::find($param['u_id']);
        $res   = $start->save($param);

        if (!$res) {
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'编辑失败');
        }
        return $this->send(ErrorCode::SUCCESS);
    }

    /**
     * @return \think\response\Json
     */
    public function delete()
    {
        $id = input('u_id');

        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS,[],'ok');
    }
}