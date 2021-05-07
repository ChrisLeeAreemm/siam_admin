<?php

namespace app\controller\admin;

use app\exception\ErrorCode;
use app\model\RolesModel;
use app\model\UsersModel;
use app\model\UsersModel as Model;
use app\plugs\apiFilter\model\PlugsApiFilterSettingModel;
use Siam\JWT;
use think\helper\Str;

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

        $result = Model::page($page, $limit)->order('u_id', 'DESC')->select()->toArray();

        foreach ($result as &$value) {
            $arr = explode(',', $value['role_id']);
            $res = RolesModel::field('role_name')->select($arr)->toArray();
            foreach ($res as $vo) {
                $role[] = $vo['role_name'];
            }
            $value['role_id'] = implode(',', $role);
        }


        $count = Model::count();
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'count' => $count]);


    }

    /**
     * 提供xmSelect的数据格式
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_xms_list()
    {
        $result  = Model::order('u_id', 'DESC')->select();
        $xmsData = [];
        foreach ($result as $v) {
            $xmsData[] = [
                'name'     => $v['u_name'],
                'value'    => $v['u_id'],
            ];
        }
        return $this->send(ErrorCode::SUCCESS, ['lists' => $xmsData]);
    }

    public function get_one()
    {
        $id     = input('u_id');
        $result = Model::find($id);
        if (!$result) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '获取失败');
        }
        $result['u_auth']  = explode(',', $result['u_auth']);
        $result['role_id'] = explode(',', $result['role_id']);
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result]);
    }

    /**
     * @return \think\response\Json
     */
    public function add()
    {

        $param     = input('data');
        $role_auth = json_decode($param['role_auth'], true);
        $role_id   = [];
        $auth      = [];

        foreach ($param as $key => $val) {
            if (!Str::startsWith($key, 'u_role')) {
                continue;
            }
            $role_id[] = $val;
        }
        foreach ($role_auth as $value) {
            $auth[] = $value['id'];
        }

        $data      = [
            'u_name'      => $param['u_name'],
            'p_u_id'      => $this->who['u_id'],
            'u_password'  => md5($param['u_password']),
            'role_id'     => implode(',', $role_id),
            'u_auth'      => implode(',', $auth),
            'create_time' => date('Y-m-d H:i:s'),
        ];
        $userModel = new UsersModel();
        $start     = $userModel->addUser($data);

        if (!$start) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '新增失败');
        }
        return $this->send(ErrorCode::SUCCESS, [], '成功');
    }

    /**
     * @return \think\response\Json
     */
    public function edit()
    {
        $param = input('data');
        $this->validate(['u_id' => 'require'], $param);

        $param['update_time'] = date('Y-m-d H:i:s');
        $role_auth            = json_decode($param['role_auth'], true);
        $role_id              = [];

        foreach ($param as $key => $val) {
            if (!Str::startsWith($key, 'u_role')) {
                continue;
            }
            $role_id[] = $val;
        }
        $auth = (new RolesModel())->recursion_roles_id($role_auth);

        $data  = [
            'u_name'      => $param['u_name'],
            'role_id'     => implode(',', $role_id),
            'u_auth'      => implode(',', $auth),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $start = Model::find($param['u_id']);
        $res   = $start->save($data);

        if (!$res) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '编辑失败');
        }
        return $this->send(ErrorCode::SUCCESS, [], '成功');
    }

    /**
     * @return \think\response\Json
     */
    public function delete()
    {
        $id = input('u_id');

        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS, [], 'ok');
    }

    public function login()
    {
        $this->validate([
            'u_account'  => 'require',
            'u_password' => 'require',
        ]);

        $password = input('u_password');

        $where = [
            'u_account' => input('u_account'),
        ];
        $user  = UsersModel::where($where)->find();
        if (!$user) return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], '用户信息不存在');

        if ($user['u_password'] !== md5($password)) {
            return $this->send(ErrorCode::AUTH_USER_CANNOT, [], '密码错误');
        }

        if ($user['u_status'] == '0') {
            return $this->send(ErrorCode::AUTH_USER_BAN, [], '用户被封禁');
        }

        $jwt      = JWT::getInstance();
        $jwtData  = $user->toArray();
        $jwtToken = $jwt->setIss('SiamAdmin')->setSecretKey('siam_admin_key')
            ->setSub("SiamAdmin")->setWith($jwtData)->make();

        return $this->send(ErrorCode::SUCCESS, [
            'token' => $jwtToken
        ], 'LOGIN_SUCCESS');
    }
}