<?php


namespace app\plugs\layuiRender\src;


class LayuiTemplet
{
    private $bodys = [];
    private $title = 'Siam Layui Render Plugs';
    private $meta = [];
    private $style = '';
    private $js = '';

    public static function make()
    {
        return new static;
    }


    public function body($content)
    {
        array_push($this->bodys, $content);
        return $this;
    }

    public function render()
    {
        // 输出基本html模板 调用accessManage 输出js代码
        $html =  "基本html";
        // body渲染
        foreach ($this->bodys as $body){
            $html .= $body;
        }

        $html.="\n<script>\n";
        $html.=AccessManage::getInstance()->renderJs();
        $html.="\n</script>";
        return $html;

    }
    public function __toString()
    {
        return $this->render();
    }
}