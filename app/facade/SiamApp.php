<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/16
 * Time: 20:43
 */

namespace app\facade;


use Siam\Component\Singleton;
use think\Console;

class SiamApp
{
    use Singleton;
    protected $module;
    protected $app;
    protected $console;

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param mixed $module
     */
    public function setModule($module): void
    {
        $this->module = $module;
    }

    /**
     * @return \think\App
     */
    public function getApp(): ?\think\App
    {
        return $this->app;
    }

    /**
     * @param mixed $app
     */
    public function setApp($app): void
    {
        $this->app = $app;
    }

    /**
     * @return mixed
     */
    public function getConsole():?Console
    {
        return $this->console;
    }

    /**
     * @param mixed $console
     */
    public function setConsole($console): void
    {
        $this->console = $console;
    }




}