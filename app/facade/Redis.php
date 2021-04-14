<?php
/**
 * redis
 * User: Administrator
 * Date: 2021/4/13
 * Time: 20:50
 */

namespace app\facade;


use Siam\Component\Singleton;

class Redis
{
    use Singleton;
    /** @var \think\cache\driver\Redis  */
    private $driver;

    public function __construct()
    {
        $config = [];
        $this->driver = new  \think\cache\driver\Redis($config);
    }

    public function init()
    {
        return $this->driver;
    }
}