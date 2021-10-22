<?php


namespace app\plugs\layuiRender\src;


abstract class BaseDom
{
    private $class = [];
    private $data = [];

    /**
     * @param string $pre
     * @return string
     */
    public function getClass(string $pre = ''): string
    {
        if (!$pre) return implode(' ', $this->class);
        return implode(' ', array_map(function ($value)use($pre) {
            return $pre.$value;
        }, $this->class));
    }

    /**
     * @param string $class
     * @return \app\plugs\layuiRender\src\BaseDom
     */
    public function setClass(string $class): BaseDom
    {
        array_push($this->class,$class);
        return $this;
    }

    /**
     * @param string $pre
     * @return string
     */
    public function getData(string $pre = ''): string
    {
        $return = '';
        foreach ($this->data as $key => $value){
            $return .= " {$pre}{$key}='{$value}' ";
        }
        return $return;
    }

    /**
     * @param string $data
     * @return \app\plugs\layuiRender\src\BaseDom
     */
    public function setData(string $data): BaseDom
    {
        array_push($this->data,$data);
        return $this;
    }

    abstract function head():string;
    abstract function foot():string;



}