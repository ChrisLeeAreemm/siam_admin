<?php

namespace app\controller\admin;

use app\common\MenuHelper;
use app\exception\ErrorCode;
use app\model\AuthsModel;
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
                    "child"  => []
                ]
            ]
        ];
        // 是否有权限  是否开启插件功能
        $siam_plugs = env("app.siam_plugs");
        if (!!$siam_plugs) {
            // 获取插件列表(带安装状态)
            $plug_arr = $this->get_plugs();
            $arr['menuInfo'][] = [
                "title"  => "组件管理",
                "icon"   => "fa fa-address-book",
                "href"   => "",
                "target" => "_self",
                "child"  => $plug_arr,
            ];
        }
        $authMenu = $this->get_auth();
        foreach ($authMenu as $value){
            array_push($arr['menuInfo'][0]['child'],$value);

        }
        return $this->send(ErrorCode::SUCCESS, $arr);

    }
    
    /**
     * 根据权限获取菜单
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_auth()
    {
        //获取权限列表
        $AuthsModel = new AuthsModel();
        $auth_list = $AuthsModel->get_admin_auths_by_u_id($this->who->u_id);
        $menu_helper = new MenuHelper();
        $tree        = $menu_helper->list_to_tree($auth_list)->getTreeAuthMenu();
        return $tree;
    }
    
    /**
     * 获取插件菜单
     * @return array|false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
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
        foreach ($arr as $dirName) {
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
                if (empty($plugsObj)){
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
                array_pop($plugs_menu);
                array_push($plugs_menu,$arr);
                if (isset($edit_arr) && $arr['title'] !== '安装'){
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

    public function is_login()
    {
        return $this->send(ErrorCode::SUCCESS);
    }
}