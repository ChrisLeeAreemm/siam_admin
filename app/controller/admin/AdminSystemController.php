<?php

namespace app\controller\admin;

use Siam\Api;
use think\db\Query;
use app\model\UsersModel as Model;
use app\BaseController;

class AdminSystemController extends BaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {
        
        $arr = [
            'homeInfo'    => [
                "title" => "首页",
                "href"  => "page/welcome-1.html?t=1"
            ],
            "logoInfo"    => [
                "title" => "LAYUI MINI",
                "image" => "images/logo.png",
                "href"  => ""
            ], "menuInfo" => [
                [
                    "title"  => "常规管理",
                    "icon"   => "fa fa-address-book",
                    "href"   => "",
                    "target" => "_self",
                    "child"  => [
                        [
                            "title"  => "主页模板",
                            "href"   => "",
                            "icon"   => "fa fa-home",
                            "target" => "_self",
                            "child"  => [
                                [
                                    "title"  => "权限",
                                    "href"   => "page/auths/lists.html",
                                    "icon"   => "fa fa-tachometer",
                                    "target" => "_self"
                                ],
                                [
                                    "title"  => "角色",
                                    "href"   => "page/roles/lists.html",
                                    "icon"   => "fa fa-tachometer",
                                    "target" => "_self"
                                ],
                                [
                                    "title"  => "用户",
                                    "href"   => "page/users/lists.html",
                                    "icon"   => "fa fa-tachometer",
                                    "target" => "_self"
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        ];
        return json($arr);
        
    }
    
    public function get_one()
    {
        $id     = input('u_id');
        $result = Model::find($id);
        if (!$result) {
            return json(['code' => '500', 'data' => '', 'msg' => '获取失败']);
        }
        return json(['code' => '200', 'data' => ['lists' => $result], 'msg' => '']);
    }
    
    /**
     * @return false|string
     */
    public function add()
    {
        
        $param = input();
        
        $start = Model::create($param);
        
        if (!$start) {
            return json(['code' => '500', 'data' => '', 'msg' => '新增失败']);
        }
        return json(['code' => '200']);
    }
    
    /**
     * @return false|string
     */
    public function edit()
    {
        $param = input();
        $start = Model::find($param['u_id']);
        $res   = $start->save($param);
        
        if (!$res) {
            return json(['code' => '500', 'data' => '', 'msg' => '编辑失败']);
        }
        return json(['code' => '200']);
    }
    
    /**
     * @return false|string
     */
    public function delete()
    {
        $id = input('u_id');
        
        $result = Model::destroy($id);
        
        return json(['code' => '200', 'msg' => 'ok']);
    }
}