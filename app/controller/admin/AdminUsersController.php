<?php

namespace app\controller\admin;

use app\exception\ErrorCode;
use app\model\UsersModel;
use app\model\UsersModel as Model;
use Siam\Api;
use Siam\JWT;

class AdminUsersController extends AdminBaseController
{
    protected $white = ['login'];

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

    public function login()
    {
        $this->validate([
            'u_account'  => 'require',
            'u_password' => 'require',
        ]);

        $password = input('u_password');

        $where = [
            'u_account'  => input('u_account'),
        ];
        $user = UsersModel::where($where)->find();
        if (!$user) return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST,[],'用户信息不存在');

        if ($user['u_password'] !== md5($password)){
            return $this->send(ErrorCode::AUTH_USER_CANNOT,[],'密码错误');
        }

        if ($user['u_status'] == '0') {
            return $this->send(ErrorCode::AUTH_USER_BAN,[],'用户被封禁');
        }

        $jwt = JWT::getInstance();
        $jwtData = $user->toArray();
        $jwtToken =  $jwt->setIss('SiamAdmin')->setSecretKey('siam_admin_key')
            ->setSub("SiamAdmin")->setWith($jwtData)->make();

        return $this->send(ErrorCode::SUCCESS, [
            'token' => $jwtToken
        ], 'LOGIN_SUCCESS');
    }
}