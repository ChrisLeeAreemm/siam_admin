<?php

namespace app\plugs\base\controller;

use app\plugs\PlugsBaseController as BaseController;

class PlugsBaseController extends BaseController
{
    public function install()
    {
        //TODO 安装,获取参数：插件名 ,执行插件的 install , 添加 install.lock ，已安装的不执行
        $this->validate(['plugsName'=>'require'],$this->request->param());
        $plugsName = $this->request->param();
    }
    
    public function status()
    {
        //TODO 开启 关闭 修改 start.plugs
    }
}