<?php

namespace app\controller\admin;


use app\BaseController;

abstract class AgentBaseController extends BaseController
{
    /** @var \app\model\UsersModel */
    public $who;

    /** 鉴权 */
    public function auth()
    {

    }
}