<?php

namespace app\plugs\base\controller;

use app\exception\ErrorCode;
use app\plugs\PlugsBaseController as BaseController;
use think\helper\Str;
use think\response\Json;

class PlugsBaseController extends BaseController
{
    /**
     * 插件安装
     * @return Json
     */
    public function install(): Json
    {
        //安装,获取插件名 , 执行插件的 install , 添加 install.lock ，已安装的不执行
        $this->validate(['plugs_name' => 'require'], $this->request->param());
        $plugsName = $this->request->param('plugs_name');
        
        $dir       = app_path() . 'plugs\\';
        $arr       = scandir($dir);
        $namespace = '\app\plugs\\';
        
        //遍历插件
        foreach ($arr as $key => $dirName) {
            if (Str::contains($dirName, '.') == false && is_dir($dir . $dirName)) {
                $Plugs      = $namespace . $dirName . '\Plugs';
                $PlugsModel = new $Plugs();
                
                //判断插件名
                $name = $PlugsModel->get_config()->getName();
                if ($name !== $plugsName) {
                    continue;
                }
                
                $lockFile = $dir . $dirName . '\install.lock';
                if (is_file($lockFile)) {
                    return $this->send(ErrorCode::SUCCESS, [], '插件已经安装过');
                }
                
                //执行安装方法
                $install = $PlugsModel->install();
                if ($install === false) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, [], '插件安装失败');
                }
                
                //生成install.lock
                $touch_lock = file_put_contents($lockFile, date('Y-m-d H:i:s'));
                if ($touch_lock === false) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, '', 'install.lock文件生成失败，请手动尝试生成');
                }
                return $this->send(ErrorCode::SUCCESS, [], '插件安装成功');
                
            }
        }
        return $this->send(ErrorCode::THIRD_PART_ERROR, [], '插件不存在');
        
        
    }
    
    /**
     * 插件状态控制
     * @return Json
     */
    public function status(): Json
    {
        //TODO 开启 关闭 修改 start.plugs
        $this->validate(['status' => 'require', 'plugs_name' => 'require'], $this->request->param());
        $status    = $this->request->param('status');
        $plugsName = $this->request->param('plugs_name');
        $startFile = app_path() . 'plugs\start.plugs';
        //base无法控制
        if ($plugsName === 'base') {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '参数无效');
        }
        //判断文件
        if (!is_file($startFile)) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], 'start.plugs file not exits');
        }
        //文件内容
        $start_arr = json_decode(file_get_contents($startFile), true);
        
        //执行开启操作
        if ($status == 'on') {
            if (!in_array($plugsName, $start_arr)) {
                array_push($start_arr, $plugsName);
                $save = file_put_contents($startFile, json_encode($start_arr));
                if ($save === false) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, [], '开启失败，写入文件错误');
                }
            }
            return $this->send(ErrorCode::SUCCESS, [], '插件开启成功');
        }
        
        //执行关闭操作
        if ($status == 'off') {
            if (in_array($plugsName, $start_arr)) {
                $arr = array_flip($start_arr);
                unset($arr[$plugsName]);
                $arr  = array_flip($arr);
                $save = file_put_contents($startFile, json_encode($arr));
                if ($save === false) {
                    return $this->send(ErrorCode::THIRD_PART_ERROR, [], '关闭失败，写入文件错误');
                }
            }
            return $this->send(ErrorCode::SUCCESS, [], '插件关闭成功');
        }
        
        return $this->send(ErrorCode::PARAM_FORMAT_ERROR, [], '参数值错误');
        
    }
}