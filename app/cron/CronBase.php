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
     * 检查运行状态
     */
    protected function needRun()
    {
        //检查状态
        $file        = runtime_path() . 'cron_status.php';
        $cron_status = json_decode(file_get_contents($file));
        $className   = basename(str_replace('\\', '/', get_class($this)));
        if (!in_array($className, $cron_status)) {
            return false;
        }
        return true;
    }

    /**
     * 执行逻辑
     * @throws \Throwable
     */
    public function run()
    {
        if ($this->needRun() === false){
            return false;
        }
        try {
            $data = $this->before();
            $res  = $this->do($data);
        } catch (\Throwable $throwable) {
            $this->clearClock();
            throw $throwable;
        }

        $this->clearClock();
        return $this->after($res);
    }

    protected function clearClock()
    {
        $lockName = static::$runtimePath . DIRECTORY_SEPARATOR . $this->rule() . ".txt";
        if (file_exists($lockName)) {
            unlink($lockName);
        }
    }

}