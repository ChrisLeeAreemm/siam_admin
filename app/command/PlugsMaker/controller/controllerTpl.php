<?php

namespace app\plugs\—PLUGS—\controller;


use app\exception\ErrorCode;

class —PLUGS—STUDLY—Controller
{
    public function get_list()
    {

        return json([
            'code' => ErrorCode::SUCCESS,
            'data' => (object) ['list'=>[]],
            'msg'  => "SUCCESS"
        ]);
    }
}