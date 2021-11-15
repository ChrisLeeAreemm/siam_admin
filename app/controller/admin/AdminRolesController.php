<?php

namespace app\controller\admin;

use app\exception\ErrorCode;
use app\model\RolesModel as Model;
use think\response\Json;

class AdminRolesController extends AdminBaseController
{
    /**
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_list()
    {

        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = Model::page($page, $limit)->order('role_id', 'DESC')->select();
        $count = Model::count();
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'count' => $count]);


    }

    /**
     * @return Json
     * @throws \app\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_one(): Json
    {
        $this->validate(['role_id' => 'require'], input());
        $id     = input('role_id');
        $result = Model::find($id);
        if (!$result) {
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], '获取失败');
        }
        $result['role_auth'] = explode(',', $result['role_auth']);
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result]);
    }

    /**
     * @return Json
     */
    public function add(): Json
    {
        $param                = input();
        $param['create_time'] = time();
        $roles_arr            = json_decode($this->request->param('role_auth'), true);
        $arr                  = [];
        foreach ($roles_arr as $value) {
            $arr[] = $value['id'];
        }
        $param['role_auth'] = implode(',', $arr);
        $start              = Model::create($param);

        if (!$start) {
            return $this->send(ErrorCode::DB_DATA_ADD_FAILE, [], '新增失败');
        }
        return $this->send(ErrorCode::SUCCESS, [], '成功');
    }

    /**
     * @return Json
     * @throws \app\exception\AuthException
     */
    public function edit(): Json
    {
        $this->validate(['role_id' => 'require'], input());

        $param                = input();
        $param['update_time'] = time();
        $roles_arr = $this->request->param('role_auth');
        try {
            $start = Model::find($param['role_id']);
        } catch (\Exception $e) {
            return $this->send(ErrorCode::DB_EXCEPTION, [], $e->getMessage());
        }

        $param['role_auth'] = implode(',', $roles_arr);
        $res                = $start->save($param);

        if (!$res) {
            return $this->send(ErrorCode::DB_DATA_UPDATE_FAILE, [], '编辑失败');
        }
        return $this->send(ErrorCode::SUCCESS,[],'成功');
    }
    
    /**
     * @return Json
     */
    public function delete(): Json
    {
        $id = input('role_id');

        $result = Model::destroy($id);
        if (!$result) {
            return $this->send(ErrorCode::DB_EXCEPTION, [], '失败');
        }

        return $this->send(ErrorCode::SUCCESS, [], '成功');

    }
}