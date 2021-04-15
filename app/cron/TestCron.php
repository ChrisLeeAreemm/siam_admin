<?php


namespace app\cron;


use Cron\CronExpression;

class TestCron extends CronBase
{

    /**
     * 写明监控任务名
     * @return mixed
     */
    function rule()
    {
        return "测试";
    }

    /**
     * 写明监控的运行周期(仅用于展示)
     * @return CronExpression
     */
    function run_period(): CronExpression
    {
        // 每天十点
        return new CronExpression('* * * * *');
    }

    /**
     * @return mixed
     */
    function before()
    {
        return [];
    }


    /**
     * @param null $data
     * @return bool
     */
    function do($data = null)
    {
        return true;
    }

    /**
     * @param bool $res
     * @return mixed
     */
    function after($res = true)
    {
        return "执行完成";
    }
}