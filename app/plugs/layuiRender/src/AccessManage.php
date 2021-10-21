<?php


namespace app\plugs\layuiRender\src;


use Siam\Component\Singleton;

class AccessManage
{
    private $css_file = [];
    private $css = [];
    private $js_file = [];
    private $js = [];
    private $layui_module = [];

    use Singleton;

    public function addJs($js){
        array_push($this->js, $js);
    }

    public function renderJs()
    {
        $return = '';
        foreach ($this->js as $js){
            $return .= $js;
        }
        return $js;
    }
}