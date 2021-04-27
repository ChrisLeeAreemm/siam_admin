<?php


namespace app\plugs\apiFilter\service;

use app\facade\Redis;
use Siam\Component\Singleton;

/**
 * Api限流器访问控制
 * Class ApiAccessContainController
 * @package app\plugs\apiFilter\controller
 */
class ApiAccessContain
{
    use Singleton;

    const API_FILTER_TOTAL = "TOTAL";
    protected $listTag    = 'filterList';
    protected $filterList = [];
    protected $handle;

    public function __construct()
    {
        $this->handle     = Redis::getInstance()->init();
        $this->filterList = $this->handle->getTagItems($this->listTag);
    }

    /**
     * 获取规则
     * @param string $filter_key
     * @return mixed
     */
    function getNumber(string $filter_key)
    {
        return $this->getAuto($filter_key)['number'];

    }

    /**
     * 请求次数
     * @param string $filter_key
     * @return int
     */
    function getAccess(string $filter_key)
    {
        return $this->getAuto($filter_key)['count'];
    }
    //TODO 流程重写


    /**
     * @param       $filter_key
     * @param bool  $auto_create
     * @return mixed
     */
    public function getAuto($filter_key, $auto_create = false)
    {
        $key = substr(md5($filter_key), 8, 16);

        $info = $this->handle->get($key);
        if ($info) {
            $this->handle->tag($this->listTag)->set($key, [
                'lastAccessTime' => time(),
                'count'          => $info['count'] + 1,
                'number'         => $info['number'] ?? -1,
            ]);
        }
        $info = [
            'filter_key'     => $filter_key,
            'lastAccessTime' => time(),
            'count'          => 1,
            'number'         => -1,
        ];
        if ($auto_create === true) {
            $this->handle->tag($this->listTag)->set($key, $info);
        }
        return $info;


    }

    /**
     * 同步Token到缓存
     * @param $filter_key
     * @param $setNumber
     */
    public function updateSetting($filter_key, $setNumber)
    {
        $info           = $this->getAuto($filter_key, true);
        $info['number'] = $setNumber;
        $key            = substr(md5($filter_key), 8, 16);
        $this->handle->tag($this->listTag)->set($key, $info);
    }

    /**
     * 清除标记缓存Count
     */
    public function clear()
    {
        foreach ($this->filterList as $value) {
            $info          = $this->handle->get($value);
            $info['count'] = 0;
            $this->handle->tag($this->listTag)->set($value, $info);
        }
    }
}