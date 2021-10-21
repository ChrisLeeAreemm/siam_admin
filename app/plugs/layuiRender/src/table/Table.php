<?php


namespace app\plugs\layuiRender\src\table;


use app\plugs\layuiRender\src\AccessManage;
use app\plugs\layuiRender\src\Grid;

class Table
{
    private $id;
    private $url;
    private $grid;

    public static function make()
    {
        return new static;
    }
    public function id($id)
    {
        $this->id = $id;
        return $this;
    }
    public function url($url){
        $this->url = $url;
        return $this;
    }
    public function grid(Grid $grid){
        $this->grid = $grid;
        return $this;
    }

    public function render()
    {
        // grid is required;

        $return = "<table id='{$this->id}' lay-filter='{$this->id}'></table>";
        $js = <<<js
table.render({
    elem: '#{$this->id}'
    ,height: 312
    ,url: '{$this->url}'
    ,page: true
    ,cols: [[
      {$this->grid->__toTableString()}
    ]]
})
js;
        AccessManage::getInstance()->addJs($js);

        return $return;

    }

    public function __toString()
    {
     return $this->render();
    }

}