<?php


namespace app\cron;


use Cron\CronExpression;

abstract class CronBase extends \Siam\AbstractInterface\CronBase
{

    /**
     * 写明监控的运行周期(仅用于展示)
     * @return CronExpression
     */
    abstract function run_period(): CronExpression;

    /**
     * 在线开关功能 传入类名数组
     * @param array $className
     * @return bool
     */
    public function changeRun(array $className): bool
    {
        $file      = runtime_path() . 'cron_status.php';
        $className = json_encode($className);
        if (!file_exists($file)) {

            $content = <<<EOL
<?php
namespace app\\runtime;
return
##start

##end
;
EOL;
            $write   = file_put_contents($file, $content);
            if (!$write) {
                return false;
            }
        }
        $content = file_get_contents($file);
        //更新文件
        $content = preg_replace('/##start.*##end/sm', $className, $content);
        $write = file_put_contents($file, $content);
        if (!$write) {
            return false;
        }
        return true;
    }

    /**
     * 执行逻辑
     * @throws \Throwable
     */
    public function run(){
        try{
            $data = $this->before();
            $res = $this->do($data);
        }catch (\Throwable $throwable){
            $this->clearClock();
            throw $throwable;
        }

        $this->clearClock();
        return $this->after($res);
    }

    protected function clearClock()
    {
        $lockName = static::$runtimePath.DIRECTORY_SEPARATOR.$this->rule().".txt";
        if (file_exists($lockName)){
            unlink($lockName);
        }
    }

}