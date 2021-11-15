<?php

namespace app\controller\admin;

use app\common\UserAuthHelper;
use app\event\EventTag;
use app\exception\ErrorCode;
use app\model\AuthsModel;
use app\model\PlugsStatusModel;
use app\model\RolesModel;
use app\model\UsersModel as Model;
use app\plugs\notice\model\PlugsNoticeModel;
use app\plugs\notice\model\PlugsNoticeReadModel;
use Siam\JWT;
use think\facade\Event;
use think\helper\Str;
use think\response\Json;

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
    
        $result = Model::page($page, $limit)->order('u_id', 'DESC')->select();
        
        //角色列表
        $roles_list = RolesModel::field('role_id,role_name')->select();
        $role_map   = [];
        foreach ($roles_list as $value) {
            $role_map[$value['role_id']] = $value['role_name'];
        }
        
        //转换角色文字
        foreach ($result as &$value) {
            //用户角色数组
            $roles_arr = explode(',', $value['role_id']);
            $role = [];
            foreach ($roles_arr as $v) {
                if (isset($role_map[$v])){
                    $role[] = $role_map[$v];
                }
            }
            $value['role_id'] = implode(',', $role);
        }
        
        $count = Model::count();
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'count' => $count]);
    }

    /**
     * 提供xmSelect的数据格式
     * @return Json
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

    /**
     * 获取用户登录配置
     * @return Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_config()
    {

        // - 应读站内信
        $where['notice_receiver'] = PlugsNoticeModel::NOTICE_RECEIVER_ALL;
        $whereOr[]                = ['notice_receiver', 'like', "%\"{$this->who->u_id}\"%"];
        $notice                   = PlugsNoticeModel::where($where)->whereOr($whereOr)->count();
        $is_read                  = PlugsNoticeReadModel::where('u_id', '=', $this->who->u_id)->count();
        $result['unread_count']   = $notice - $is_read;

        // - 所有插件状态
        $plugs_status = PlugsStatusModel::where('plugs_name', '<>', 'base')->field('plugs_name,plugs_status')->select();
        if ($plugs_status){
            foreach ($plugs_status as $v){
                $result['plugs_status'][$v['plugs_name']]['status'] = $v['plugs_status'];
            }
        }

        // - 所有插件状态

        // - 个人信息
        /** @var \app\model\UsersModel $user */
        $user             = Model::field('u_name,u_auth')->where('u_id', '=', $this->who->u_id)->find();
        $result['u_auth'] = UserAuthHelper::get_list_by_user($user); //权限
        $result['u_name'] = $user->u_name; //用户名
        // - 个人信息

        // - 未读的强制阅读通知列表

        //7天内
        $day            = date('Y-m-d H:i:s', strtotime("-7 day"));
        $where_receiver = [
            ['notice_receiver', '=', PlugsNoticeModel::NOTICE_RECEIVER_ALL],
        ];
        $where_force    = [
            ['notice_type', '=', PlugsNoticeModel::NOTICE_TYPE_FORCE],
            ['create_time', '>=', $day]
        ];
        //获取符合的数据
        $force_notice = PlugsNoticeModel::where([$where_force])->where(function ($query) use ($whereOr, $where_receiver) {
            $query->where($where_receiver)->whereOr($whereOr);
        })->select();

        $notice_ids   = [];
        //取出ID数组
        foreach ($force_notice as $v) {
            $notice_ids[] = $v['notice_id'];
        }

        //对比取出未读数据
        $read_notice = PlugsNoticeReadModel::where('u_id', '=', $this->who->u_id)->whereIn('notice_id', implode(',', $notice_ids))->select();
        if ($read_notice) {
            foreach ($force_notice as $key => $value) {
                foreach ($read_notice as $vo) {
                    if ($value['notice_id'] !== $vo['notice_id']) continue;
                    //把已读的从数据中中删除
                    unset($force_notice[$key]);
                }
            }
            unset($read_notice);
        }
        $result['force_notice'] = array_values($force_notice->toArray());
        // - 未读的强制阅读通知列表

        return $this->send(ErrorCode::SUCCESS, $result);

    }

    public function get_one(): Json
    {
        $id     = input('u_id');
        $result = Model::find($id);
        if (!$result) {
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], '获取失败');
        }
        $result['u_auth']  = explode(',', $result['u_auth']);
        $result['role_id'] = explode(',', $result['role_id']);
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result]);
    }

    /**
     * @return Json
     */
    public function add(): Json
    {
        $param     = input('data');
        //判断是否存在
        $account_exist = Model::where('u_account', $param['u_account'])->count();
        if ($account_exist){
            return $this->send(ErrorCode::DB_DATA_ALREADY_EXIST, [], '该账号已存在');
        }
        
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
            'u_account'   => $param['u_account'],
            'p_u_id'      => $this->who['u_id'],
            'u_password'  => md5($param['u_password']),
            'role_id'     => implode(',', $role_id),
            'u_auth'      => implode(',', $auth),
            'create_time' => date('Y-m-d H:i:s'),
        ];
        $userModel = new Model();
        $start     = $userModel->addUser($data);

        if (!$start) {
            return $this->send(ErrorCode::DB_DATA_ADD_FAILE, [], '新增失败');
        }
        return $this->send(ErrorCode::SUCCESS, [], '成功');
    }

    /**
     * @return Json
     */
    public function edit()
    {
        $param = input('data');
        $this->validate(['u_id' => 'require'], $param);
    
        $users = Model::find($param['u_id']);
        if (!$users){
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], 'DB_DATA_DOES_NOT_EXIST');
        }
        //判断是否存在
        $account_exist = Model::where('u_account', $param['u_account'])->find();
        if ($account_exist && $account_exist['u_id'] != $param['u_id']) {
            return $this->send(ErrorCode::DB_DATA_ALREADY_EXIST, [], '该账号已存在');
        }

        $role_auth            = $param['role_auth'];
        $role_id              = [];
        foreach ($param as $key => $val) {
            if (!Str::startsWith($key, 'u_role')) {
                continue;
            }
            $role_id[] = $val;
        }

        $data  = [
            'u_name'      => $param['u_name'],
            'u_password'  => md5($param['u_password']),
            'u_account'   => $param['u_account'],
            'role_id'     => implode(',', $role_id),
            'u_auth'      => implode(',', $role_auth),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        if ($users->u_password !== $param['u_password'] && !empty($param['u_password'])){
            $data['u_password'] = md5($param['u_password']);
        }
        $res   = $users->save($data);
        if (!$res) {
            return $this->send(ErrorCode::DB_DATA_UPDATE_FAILE, [], '编辑失败');
        }
        return $this->send(ErrorCode::SUCCESS, [], '成功');
    }

    /**
     * @return Json
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
        $user  = Model::where($where)->find();
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
        Event::trigger(EventTag::REGISTER_TOKEN,['u_id'=>$user['u_id'],'token'=>$jwtToken]);

        return $this->send(ErrorCode::SUCCESS, [
            'token' => $jwtToken
        ], 'LOGIN_SUCCESS');
    }

    /**
     * 修改密码
     * @return Json
     * @throws \app\exception\AuthException
     */
    public function edit_pwd()
    {
        $this->validate([
            'u_password'     => 'require',
            'old_u_password' => 'require',
        ], input('data'));

        $params = input('data');

        if (md5($params['old_u_password']) !== $this->who->u_password) {
            return $this->send(ErrorCode::AUTH_USER_CANNOT, [], '原密码错误');
        }

        if (md5($params['u_password']) === $this->who->u_password) {
            return $this->send(ErrorCode::AUTH_USER_CANNOT, [], '新密码不能和原密码一样');
        }

        $update = Model::update(['u_password' => md5($params['u_password'])], ['u_id' => $this->who->u_id]);
        if (!$update) {
            return $this->send(ErrorCode::DB_DATA_UPDATE_FAILE, [], '密码修改失败');
        }

        return $this->send(ErrorCode::SUCCESS, [], '修改成功,请重新登录');
    }

    public function logout()
    {
        $token = input('access_token');
        Event::trigger(EventTag::DESTORY_TOKEN, $token);
        return $this->send(ErrorCode::SUCCESS,[], 'LOGOUT_SUCCESS');
    }
}