<?php

namespace app\controller\admin;

use think\db\Query;
use app\model\UsersModel as Model;
use app\BaseController;
use think\helper\Arr;
use think\helper\Str;

class AdminSystemController extends BaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {
        //TODO 2.启用插件 3.停用插件
        
        // 获取插件列表(带安装状态)
        $plug_arr = $this->get_plugs();
        $arr = [
            'homeInfo' => [
                "title" => "首页",
                "href"  => "page/welcome-1.html?t=1"
            ],
            "logoInfo" => [
                "title" => "LAYUI MINI",
                "image" => "images/logo.png",
                "href"  => ""
            ],
            "menuInfo" => [
                [
                    "title"  => "常规管理",
                    "icon"   => "fa fa-address-book",
                    "href"   => "",
                    "target" => "_self",
                    "child"  => [
                        [
                            "title"  => "管理列表",
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
                ],
                [
                    "title"  => "组件管理",
                    "icon"   => "fa fa-address-book",
                    "href"   => "",
                    "target" => "_self",
                    "child"  => [
                        [
                            "title"  => "组件管理",
                            "href"   => "",
                            "icon"   => "fa fa-home",
                            "target" => "_self",
                            "child"  => $plug_arr
                        ],
                    ]
                ]
            ]
        ];
        return json($arr);
        
    }
    
    public function get_plugs()
    {
        $dir = app_path() . 'plugs\\';
        if (!is_dir($dir)) {
            return false;
        }
        $domain = $this->request->domain();
        $arr    = scandir($dir);
        //检索插件
        $namespace = '\app\plugs\\';
        $child     = [];
        foreach ($arr as $key => $dirName) {
            if (Str::contains($dirName, '.') == false && is_dir($dir . $dirName)) {
                $Plugs      = $namespace . $dirName . '\Plugs';
                $PlugsModel = new $Plugs();
                $name       = $PlugsModel->get_config()->getName();
                $HomeView = $PlugsModel->get_config()->getHomeView();
                $href = '';
                if( strpos($HomeView, 'http') != false || strpos($HomeView, '#')){
                    $href = $HomeView;
                }
                if( strpos($HomeView, '/') != false){
                    $href = '/index.php/'.$HomeView;
                }
                $child[]    = [
                    'title'  => $name,
                    'href'   => $href,
                    'icon'   => "fa fa-tachometer",
                    'target' => '_self'
                ];
            }
        }
        return $child;
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