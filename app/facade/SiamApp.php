<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/16
 * Time: 20:43
 */

namespace app\facade;


use Siam\Component\Singleton;

class SiamApp
{
    use Singleton;
    protected $module;

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

}