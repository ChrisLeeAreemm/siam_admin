<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2021/4/12
 * Time: 8:44
 */

namespace app\controller\admin;

use app\BaseController;

abstract class AdminBaseController extends BaseController
{
    /** @var \app\model\UsersModel */
    public $who;

    /** 鉴权 */
    public function auth()
    {

    }
}