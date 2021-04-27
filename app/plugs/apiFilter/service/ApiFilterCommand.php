<?php


namespace app\plugs\apiFilter\service;

use app\plugs\apiFilter\model\PlugsApiFilterSettingModel;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Crontab\Crontab;
use Workerman\Lib\Timer;
use Workerman\Worker;

class ApiFilterCommand extends Command
{
    protected function configure()
    {
        $this->setName('api filter')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, 'Run the workerman server in daemon mode.')
            ->setDescription('执行apifilter');
    }

    /**
     * @param \think\console\Input  $input
     * @param \think\console\Output $output
     * @return void
     * @throws \Throwable
     */
    protected function execute(Input $input, Output $output)
    {
        // 指令输出
        $output->writeln('convert start');
        $action = $input->getArgument('action');
        $mode = $input->getOption('mode');
        // 重新构造命令行参数,以便兼容workerman的命令
        global $argv;
        $argv = [];
        array_unshift($argv, 'think', $action);
        if ($mode == 'd') {
            $argv[] = '-d';
        } else if ($mode == 'g') {
            $argv[] = '-g';
        }

        $worker = new Worker();
        $worker->onWorkerStart = function () {
            //  定时器 1分钟同步一次token到缓存
            Timer::add(60,function (){
                    ApiAccessContain::getInstance()->updateSetting();
            });
            // 定时器 1秒一次限流检测
            Timer::add(1,function (){
                ApiAccessContain::getInstance()->reset();
            });

        };

        Worker::runAll();
    }

}