<?php


namespace app\cron;


abstract class CronBase extends \Siam\AbstractInterface\CronBase
{

    /**
     * 写明监控的运行周期(仅用于展示)
     * @return string
     */
    abstract function run_period(): string;

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