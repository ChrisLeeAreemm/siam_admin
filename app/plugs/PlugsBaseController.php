<?php


namespace app\plugs;


use app\BaseController;
use app\exception\AuthException;
use app\exception\ErrorCode;
use app\model\UsersModel;
use Siam\JWT;

abstract class PlugsBaseController extends BaseController
{
    protected $white = [];
    /** @var \app\model\UsersModel */
    public $who;

    /**
     * 在插件内部验证当前登录账号的信息
     * @return UsersModel|void
     * @throws AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function auth()
    {
        if (in_array($this->request->action(), $this->white)) {
            return;
        }
        // 解析token 如果超时或者错误则返回 重新登录的状态码 前端跳转到登录页
        $token = input('access_token');
        if (!$token) throw new AuthException("token不可为空", ErrorCode::AUTH_NEED_LOGIN_AGAIN);

        try {
            $jwt = JWT::getInstance()->setSecretKey('siam_admin_key')->decode($token);
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage(), ErrorCode::AUTH_NEED_LOGIN_AGAIN);
        }

        $user = UsersModel::where([
            'u_id' => $jwt['u_id']
        ])->find();
        if (!$user) {
            throw new AuthException("该token用户不存在", ErrorCode::AUTH_USER_NONE);
        }
        $this->who = $user;
    }

    function get_list()
    {
    }

    function add()
    {
    }

    function edit()
    {
    }

    function delete()
    {
    
    }

}