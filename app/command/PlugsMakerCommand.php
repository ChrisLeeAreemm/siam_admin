<?php
/**
 * 命令行新建插件支持
 * User: Administrator
 * Date: 2021/4/15
 * Time: 20:22
 */

namespace app\command;


use EasySwoole\Utility\File;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\helper\Str;

class PlugsMakerCommand extends Command
{
    protected function configure()
    {
        $this->setName('plugs')
            ->addOption('name', null, Option::VALUE_REQUIRED, '插件名')
            ->setDescription('创建新插件');
    }

    protected function execute(Input $input, Output $output)
    {
        $plugs_name = $input->getOption('name');
        if (!$plugs_name) {
            echo "name必传";
            return;
        }
        // 插件名必须是小驼峰
        $plugs_name = Str::camel($plugs_name);
        // 插件是否存在
        $plugs_dir = app_path().DIRECTORY_SEPARATOR."plugs".DIRECTORY_SEPARATOR.$plugs_name.DIRECTORY_SEPARATOR;
        if (is_dir($plugs_dir)){
            echo "该名称已被占用";
            return ;
        }
        $plugs_studly = Str::studly($plugs_name);
        $plugs_snake  = Str::snake($plugs_name);

        $tpl_dir = __DIR__."/PlugsMaker/";
        File::copyDirectory($tpl_dir, $plugs_dir);

        $files = [
            "Plugs.php",
            "view/index.html",
            "controller/controllerTpl.php" => "controller/{$plugs_studly}Controller.php"
        ];
        foreach ($files as $key => $value){
            if (is_string($key)){
                // 重命名文件
                rename($plugs_dir.$key, $plugs_dir.$value);
            }
            $tpl = file_get_contents($plugs_dir.$value);
            $tpl = str_replace("—PLUGS—SNAKE—", $plugs_snake, $tpl);
            $tpl = str_replace("—PLUGS—STUDLY—", $plugs_studly, $tpl);
            $tpl = str_replace("—PLUGS—", $plugs_name, $tpl);
            file_put_contents($plugs_dir.$value, $tpl);
        }

        echo "插件新建完成";
    }
}