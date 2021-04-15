<?php


namespace app\cron;


abstract class CronBase extends \Siam\AbstractInterface\CronBase
{

    /**
     * 写明监控的运行周期(仅用于展示)
     * @return string
     */
    abstract function run_period(): string;
}