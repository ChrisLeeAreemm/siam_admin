<?php


namespace app\plugs\apiFilter\service;

use app\facade\Redis;
use app\plugs\apiFilter\model\PlugsApiFilterSettingModel;
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
    
    
    /**
     * @param       $filter_key
     * @return array
     */
    public function getAuto($filter_key)
    {
        $key  = substr(md5($filter_key), 8, 16);
        $info = $this->handle->get($key);
        $arr  = [
            'filter_key'     => $filter_key,
            'lastAccessTime' => time(),
        ];
        if (!$info) {
            $arr['count']  = -1;
            $arr['number'] = -1;
            return $arr;
        }
        $arr['count']  = $info['count'] + 1;
        $arr['number'] = $info['number'];
        $this->handle->tag($this->listTag)->set($key, $arr);
        
        return $arr;
        
        
    }

    /**
     * 同步Token到缓存
     * @return false
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateSetting()
    {
        //清空旧缓存
        $this->handle->tag($this->listTag)->clear();
        //更新数据
        $setting = PlugsApiFilterSettingModel::select();
        if (!$setting){
            return false;
        }
        foreach ($setting as $value) {
            $info           = $this->getAuto($value['key']);
            $info['number'] = $value['number'];
            $key            = substr(md5($value['key']), 8, 16);
            $this->handle->tag($this->listTag)->set($key, $info);
        }
        
        
    }
    
    /**
     * 重置缓存Count
     */
    public function reset()
    {
        foreach ($this->filterList as $value) {
            $info          = $this->handle->get($value);
            $info['count'] = 0;
            $this->handle->tag($this->listTag)->set($value, $info);
        }
    }
}