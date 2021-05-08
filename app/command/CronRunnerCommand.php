<?php


namespace app\command;

use app\plugs\base\service\PlugsCommandStatus;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Crontab\Crontab;
use Workerman\Lib\Timer;
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
                // FEATURE 把任务和cron id 记录保存起来，然后定时器定时扫描文件 看看是否更改
                new Crontab("0 {$class->run_period()}", function() use($class){
                    try{
                        // 保存到对应的日志文件中
                        ob_start();
                        echo "=============\n";
                        echo date("Y-m-d H:i:s") . "\n";
                        echo $class->run();
                        echo "\n=============\n";
                        $content = ob_get_contents();
                        ob_end_clean();
                        $file_path = runtime_path("cron");
                        if (!is_dir($file_path)){
                            mkdir($file_path);
                        }
                        $file_path .= $class->rule().".log";
                        file_put_contents($file_path, $content, FILE_APPEND);
                    }catch (\Throwable $throwable){
                        var_dump($throwable->getMessage());
                    }
                });
                echo "已注册 {$class->rule()}\n";
            }

            // 定时器 任务心跳包
            // FEATURE 扫描文件 看看运行规则是否更改
            Timer::add(1,function (){
                PlugsCommandStatus::ping('cron');
            });
        };


        Worker::runAll();
    }
}