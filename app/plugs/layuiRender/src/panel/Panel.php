<?php


namespace app\plugs\layuiRender\src\panel;


class Panel
{
    private $title;
    private $content;

    public static function make()
    {
        return new static();
    }
    public function title($title)
    {
        $this->title = $title;
        return $this;

    }
    public function content($content){
        $this->content = $content;
        return $this;
    }

    public function render()
    {
        return <<<html
<div class="layui-card">
  <div class="layui-card-header">{$this->title}</div>
  <div class="layui-card-body">
  {$this->content}
  </div>
</div>
html;
    }
    public function __toString()
    {
        return $this->render();
    }
}