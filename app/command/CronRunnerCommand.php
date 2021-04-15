<?php


namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;

class CronRunnerCommand extends Command
{
    protected function configure()
    {
        $this->setName('cron runner')
            ->addOption('name', null, Option::VALUE_REQUIRED, 'cron任务类名')
            ->setDescription('执行cron任务');
    }

    protected function execute(Input $input, Output $output)
    {
        $cron_name = $input->getOption('name');
        if (!$cron_name) {
            echo "name必传";
            return;
        }
        $namespace = "app\\cron\\";
        $class_name = $namespace.$cron_name;
        $runner = new $class_name();
        $return = $runner->run();
        echo $return;
    }
}