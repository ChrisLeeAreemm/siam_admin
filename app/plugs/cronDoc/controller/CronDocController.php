<?php

namespace app\plugs\cronDoc\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;

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
                'status'         => $this->get_status($class_name),
            ];
        }
        return $this->send(ErrorCode::SUCCESS,['list'=>$return],'SUCCESS');
    }

    /**
     * 获取状态
     * @param $class_name
     * @return bool
     */
    public function get_status($class_name)
    {
        $file      = runtime_path() . 'cron_status.php';
        if (!file_exists($file)) {
            return false;
        }
        $file_arr = json_decode(file_get_contents($file),true);
        return in_array($class_name, $file_arr);
    }

    /**
     * 在线开关
     * type 1 开启 2关闭
     * @param string $className 类名
     * @return bool|\think\response\Json
     */
    public function online_switch()
    {
        $this->validate(['className' => 'require', 'type' => 'require'], input());
        $type      = input('type');
        $file      = runtime_path() . 'cron_status.php';
        $className = input('className');

        //不存在文件的情况下
        if (!file_exists($file)) {
            //关闭的直接返回成功
            if ($type === 2) {
                return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
            }
            $arr[] = $className;
            $className = json_encode($arr);
            //创建
            $content = <<<EOL
$className
EOL;
            $write   = file_put_contents($file, $content);
            if (!$write) {
                return $this->send(ErrorCode::FILE_WRITE_FAIL, [], 'FILE_WRITE_FAIL');
            }
            return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
        }

        $file_arr = json_decode(file_get_contents($file),true);
        //开启
        if ($type == 1) {
            //存在则不改变
            if (in_array($className, $file_arr)) {
                return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
            }
            $file_arr[] = $className;
        }
        //关闭
        if ($type == 2) {
            if (!in_array($className, $file_arr)) {
                return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
            }
            //删除
            foreach ($file_arr as $key => $value) {
                if ($value === $className) {
                    unset($file_arr[$key]);
                }
            }
        }
        $file_arr = json_encode($file_arr);
        $content  = <<<EOL
$file_arr
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