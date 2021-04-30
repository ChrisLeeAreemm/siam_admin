<?php

namespace app\plugs\cronDoc\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;
use think\helper\Str;

class CronDocController extends PlugsBaseController
{
    public function get_list()
    {
        $file_path = app_path().DIRECTORY_SEPARATOR."cron";
        $list = scandir($file_path);
        unset($list[0]);
        unset($list[1]);
        $list = array_values($list);
        $return = [];
        $namespace = 'app\\cron\\';
        foreach ($list as $file_name){
            $class_name = rtrim($file_name, '.php');
            if ($class_name === 'CronBase') continue;
            $class_namespace = $namespace.$class_name;
            /** @var \app\cron\CronBase $class */
            $class = new $class_namespace;
            $return[] = [
                'name'           => $class->rule(),
                'run_expression' => $class->run_period()->getExpression(),
                'next_run_time'  => $class->run_period()->getNextRunDate()->format("Y-m-d H:i:s"),
                'class_name'     => $class_name,
                'status'         => $this->switchStatus($class_name),
            ];
        }
        return $this->send(ErrorCode::SUCCESS,['list'=>$return],'SUCCESS');
    }

    public function switchStatus($class_name)
    {
        $file      = runtime_path() . 'cron_status.php';
        $file_arr = include($file);
        //存在则不改变
        if (!in_array($class_name, $file_arr)) {
            return false;
        }
        return true;
    }

    /**
     * 在线开关
     * type 1 开启 2关闭
     * @param array $className 类名 一维数组 ['classname']
     * @return bool|\think\response\Json
     */
    public function online_switch()
    {
        $this->validate(['className' => 'require', 'type' => 'require'], input());
        $type      = input('type');
        $file      = runtime_path() . 'cron_status.php';
        $className = json_encode(input('className'));


        //不存在文件的情况下
        if (!file_exists($file)) {
            //关闭的直接返回成功
            if ($type === 2) {
                return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
            }
            //创建
            $content = <<<EOL
<?php
#start
return $className;
#end
EOL;
            $write   = file_put_contents($file, $content);
            if (!$write) {
                return $this->send(ErrorCode::FILE_WRITE_FAIL, [], 'FILE_WRITE_FAIL');
            }
            return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
        }


        $classKey = input('className')[0];
        //开启
        if ($type == 1) {
            $file_arr = include($file);
            //存在则不改变
            if (in_array($classKey, $file_arr)) {
                return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
            }
            $file_arr[] = $classKey;
        }
        //关闭
        if ($type == 2) {
            $file_arr = include($file);
            if (!in_array($classKey, $file_arr)) {
                return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
            }
            //删除
            foreach ($file_arr as $key => $value) {
                if ($value === $classKey) {
                    unset($file_arr[$key]);
                }
            }
        }
        $file_arr = json_encode($file_arr);
        $content  = <<<EOL
<?php
#start
return $file_arr;
#end
EOL;


        //更新文件
        $content = preg_replace('/#start.*#end/m', $className, $content);
        $write   = file_put_contents($file, $content);
        if (!$write) {
            return $this->send(ErrorCode::FILE_WRITE_FAIL, [], 'FILE_WRITE_FAIL');
        }
        return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
    }

}