<?php


namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\facade\Log;
use Workerman\Crontab\Crontab;
use Workerman\Worker;

class CronRunnerCommand extends Command
{
    protected function configure()
    {
        $this->setName('cron runner')
            ->addArgument('action', Argument::OPTIONAL, "start|stop|restart|reload|status|connections", 'start')
            ->addOption('mode', 'm', Option::VALUE_OPTIONAL, 'Run the workerman server in daemon mode.')
            ->setDescription('执行cron任务');
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
            $file_path = app_path().DIRECTORY_SEPARATOR."cron";
            $list = scandir($file_path);
            unset($list[0]);
            unset($list[1]);
            $list = array_values($list);

            $namespace = 'app\\cron\\';
            foreach ($list as $file_name) {
                $class_name = rtrim($file_name, '.php');
                if ($class_name === 'CronBase') continue;
                $class_namespace = $namespace.$class_name;
                /** @var \app\cron\CronBase $class */
                $class = new $class_namespace;

                new Crontab("0 {$class->run_period()}", function() use($class){
                    echo "=============\n";
                    echo date("Y-m-d H:i:s") . "\n";
                    echo $class->run();
                    echo "\n=============\n";
                });
                echo "已注册 {$class->rule()}\n";
            }
        };


        Worker::runAll();
    }
}