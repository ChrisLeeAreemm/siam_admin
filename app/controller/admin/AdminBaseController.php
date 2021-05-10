<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2021/4/12
 * Time: 8:44
 */

namespace app\controller\admin;

use app\BaseController;
use app\event\EventTag;
use app\exception\AuthException;
use app\exception\ErrorCode;
use app\model\UsersModel;
use Siam\JWT;
use think\facade\Event;

abstract class AdminBaseController extends BaseController
{
    protected $white = [];
    /** @var \app\model\UsersModel */
    public $who;

    /** 鉴权
     * @throws \app\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function auth()
    {
        if (in_array($this->request->action(), $this->white)){
            return ;
        }
        // 解析token 如果超时或者错误则返回 重新登录的状态码 前端跳转到登录页
        $token = input('access_token');
        if (!$token) throw new AuthException("token不可为空", ErrorCode::AUTH_NEED_LOGIN_AGAIN);
        // 交由其他管理器先行验证一次 如果验证不通过 则抛出异常
        Event::trigger(EventTag::AUTH_TOKEN, $token);
        try {
            $jwt = JWT::getInstance()->setSecretKey('siam_admin_key')->decode($token);
        } catch (\Exception $e) {
            throw new AuthException($e->getMessage(), ErrorCode::AUTH_NEED_LOGIN_AGAIN);
        }

        $user = UsersModel::where([
            'u_id' => $jwt['u_id']
        ])->find();
        if (!$user){
            throw new AuthException("该token用户不存在", ErrorCode::AUTH_USER_NONE);
        }
        $this->who = $user;

    }
}