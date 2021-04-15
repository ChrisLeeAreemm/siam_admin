<?php


namespace app\cron;


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
     * @return string
     */
    function run_period(): string
    {
        return "每天10点";
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