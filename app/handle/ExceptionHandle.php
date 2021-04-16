<?php
/**
 * 异常接管
 * User: Administrator
 * Date: 2021/4/13
 * Time: 20:47
 */

namespace app\handle;


use app\exception\BaseException;
use think\exception\Handle;
use think\exception\ValidateException;
use think\Response;
use Throwable;

class ExceptionHandle extends Handle
{
    public function render($request, Throwable $e): Response
    {
        // TODO 是否安装异常记录插件，是则插入


        if ($e instanceof BaseException){
            return json([
                'code' => $e->getCode(),
                'data' => $e->get_return(),
                'msg'  => $e->getMessage(),
            ]);
        }
        if ($e instanceof ValidateException){
            return json([
                'code' => $e->getCode(),
                'data' => [],
                'msg'  => $e->getMessage(),
            ]);
        }
        return parent::render($request, $e);
    }
}