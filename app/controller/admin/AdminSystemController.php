<?php

namespace app\controller\admin;

use app\exception\ErrorCode;
use app\model\PlugsStatusModel;
use app\model\UsersModel as Model;
use app\plugs\base\Plugs as BasePlugs;
use think\helper\Str;

class AdminSystemController extends AdminBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {
        
        // 获取插件列表(带安装状态)
        $plug_arr = $this->get_plugs();
        $arr      = [
            'homeInfo' => [
                "title" => "首页",
                "href"  => "page/welcome-1.html?t=1"
            ],
            "logoInfo" => [
                "title" => env('app.app_name'),
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
                    "child"  => $plug_arr
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

        $arr = scandir($dir);
        //检索插件
        $namespace = '\app\plugs\\';
        $child     = [];
        foreach ($arr as $key => $dirName) {
            if (Str::contains($dirName, '.') == false && is_dir($dir . $dirName)) {
                $Plugs      = $namespace . $dirName . '\Plugs';
                /** @var \app\plugs\PlugsBase $plugs */
                $plugs = new $Plugs();
                $name       = $plugs->get_config()->getName();
                //过滤base,不需要独立显示
                if ($name == 'base') {
                    continue;
                }

                //组装菜单  读取插件的状态，显示对应的按钮和名称
                $plugsObj = PlugsStatusModel::find($name);
                
                //获取Base 菜单
                $basePlugs = new BasePlugs();
                $baseMenu = $basePlugs->get_config()->getMenu();
                $temp_key = array_column($baseMenu,'title');  //键值
                $baseMenu = array_combine($temp_key,$baseMenu);
                // 未安装状态 ： 只显示 名称 + 安装项
                if (!$plugsObj){
                    $arr = $baseMenu['安装'];
                    $arr['href'] .= '?plugs_name='.$name;
                }else{
                    // 安装并启动状态： 只显示 名称 + 停用项
                    if ($plugsObj['plugs_status'] == PlugsStatusModel::PLUGS_STATUS_ON){
                        $arr = $baseMenu['停用'];
                        $arr['href'] .= '?plugs_name='.$name.'&status=off';
                    }

                    // 安装未启动状态： 只显示 名称 + 启动项
                    if ($plugsObj['plugs_status'] == PlugsStatusModel::PLUGS_STATUS_OFF){
                        $arr = $baseMenu['启用'];
                        $arr['href'] .= '?plugs_name='.$name.'&status=on';
                    }
                    //编辑页
                    $edit_arr = $baseMenu['编辑'];
                    $edit_arr['href'] .= '?plugs_name='.$name;
                }

                $plugs_menu = [];
                if (!empty($plugs->get_config()->getMenu()) && $arr['title'] !== '启用' && $arr['title'] !== '安装'){
                    foreach ($plugs->get_config()->getMenu() as $one){
                        if (!Str::startsWith($one['href'],'/page') && !Str::startsWith($one['href'],'page') && !Str::startsWith($one['href'],'http')){
                            $one['href'] = "/index.php/".$one['href'];
                            $one['href'] = str_replace("//", "/", $one['href']);
                        }
                        array_push($plugs_menu, $one);
                    }
                }
                array_push($plugs_menu,$arr);
                if (isset($edit_arr)){
                    array_push($plugs_menu,$edit_arr);
                }

                $child[] = [
                    'title'  => $name,
                    'href'   => '',
                    'icon'   => "",
                    'target' => '_self',
                    'child'  => $plugs_menu
                ];
            }
        }
        return $child;
    }
    
    public function get_one()
    {
        $id     = input('u_id');
        $result = Model::find($id);
        if (!$result){
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'获取失败');
        }
        return $this->send(ErrorCode::SUCCESS,['lists'=>$result]);
    }
    
    
    /**
     * @return \think\response\Json
     */
    public function add()
    {
        
        $param = input();
        
        $start = Model::create($param);

        if (!$start) {
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'新增失败');
        }
        return $this->send(ErrorCode::SUCCESS);
    }
    
    /**
     * @return \think\response\Json
     */
    public function edit()
    {
        $param = input();
        $start = Model::find($param['u_id']);
        $res   = $start->save($param);

        if (!$res){
            return $this->send(ErrorCode::THIRD_PART_ERROR,[],'编辑失败');

        }
        return $this->send(ErrorCode::SUCCESS);
    }
    
    /**
     * @return \think\response\Json
     */
    public function delete()
    {
        $id = input('u_id');
        
        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS,[],'ok');

    }
}