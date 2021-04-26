<?php


namespace app\plugs;


use app\BaseController;
use app\exception\AuthException;
use app\exception\ErrorCode;
use app\model\UsersModel;

abstract class PlugsBaseController extends BaseController
{
    /**
     * 在插件内部验证当前登录账号的信息
     */
    public function auth(): UsersModel
    {
        $user = UsersModel::find(1);;
        if (!$user){
            throw new AuthException(ErrorCode::AUTH_USER_NONE);
        }
        return  $user;
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