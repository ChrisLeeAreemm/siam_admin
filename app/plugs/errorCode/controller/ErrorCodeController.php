<?php

namespace app\plugs\errorCode\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;
use think\helper\Str;

class ErrorCodeController extends PlugsBaseController
{
    public function get_list()
    {
        $file_path = app_path().DIRECTORY_SEPARATOR."exception".DIRECTORY_SEPARATOR."ErrorCode.php";
        $content = file_get_contents($file_path);
        $array = explode("\n", $content);
        $return = [];
        foreach ($array as $row){
            // 是否以const开头
            $row = trim($row);
            if (substr($row, 0, 5) === "const"){
                // 匹配
                $row = explode(" ", $row);
                $return[] = [
                    'field' => $row[1],
                    'code'  => Str::substr($row[3],1, 3),
                    'msg'   => $row[4],
                ];
            }
        }
        return json([
            'code' => ErrorCode::SUCCESS,
            'data' => (object) ['list'=>$return],
            'msg'  => "SUCCESS"
        ]);
    }

    function auth()
    {

    }
}