<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/18
 * Time: 17:24
 */

namespace app\handle;


use think\App;
use think\exception\ClassNotFoundException;
use think\helper\Str;
use think\route\dispatch\Controller;

class RouterDispatch extends Controller
{
    public function init(App $app)
    {
        $router_path = $this->request->pathinfo();
        $router_path = explode('/', $router_path);
        list($module, $controller_name, $action_name) = $router_path;
        $controller_full_name = Str::studly($module).Str::studly($controller_name)."Controller";

        $this->dispatch = [
            "{$module}.{$controller_full_name}",
            "{$action_name}"
        ];
        parent::init($app);
    }

    public function exec()
    {
        return parent::exec();
    }
}