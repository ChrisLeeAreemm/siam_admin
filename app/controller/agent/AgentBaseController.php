<?php

namespace app\controller\admin;


use app\BaseController;

class AgentBaseController extends BaseController
{
    /**
     * @param        $code
     * @param array  $data
     * @param string $msg
     * @return \think\response\Json
     */
    public function json($code, $data = [], $msg = '')
    {
        return json([
            'code' => $code,
            'data' => (object) $data,
            'msg'  => $msg
        ]);
    }
}