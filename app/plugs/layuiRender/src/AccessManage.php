<?php


namespace app\plugs\layuiRender\src;


use Siam\Component\Singleton;

class AccessManage
{
    private $css_file = [
        "/admin/lib/layui-v2.6.3/css/layui.css",
        "/admin/css/public.css",
    ];
    private $js_file = [
        "/admin/lib/layui-v2.6.3/layui.js",
        "/admin/js/lay-config.js?v=2.0.4",
    ];
    private $css = [];
    private $js = ["
if (!!layui.jquery && !$){
    var $ = layui.jquery;
}"];
    private $layui_module = ['jquery'];

    use Singleton;

    public function addJs($js, $module = ''){
        array_push($this->js, $js);
        if (!!$module){
            $this->addModule($module);
        }
    }
    public function addModule($module){
        // 去重
        if (array_search($module, $this->layui_module) !== false) return ;
        array_push($this->layui_module, $module);
    }

    public function renderJs(): string
    {
        $return = '';
        foreach ($this->js as $js){
            $return .= "\n".$js."\n";
        }
        return $return;
    }

    public function getCssFile(): string
    {
        $return = '';
        foreach ($this->css_file as $css){
            $return .= "<link rel='stylesheet' type='text/css' href='{$css}' />\n";
        }
        return $return;
    }

    public function getJsFile(): string
    {

        $return = '';
        foreach ($this->js_file as $js){
            $return .= "<script src='{$js}'></script>\n";
        }
        return $return;
    }

    public function getModule(): string
    {
        return implode(',', array_map(function($value){
            return sprintf("'%s'", $value);
        }, $this->layui_module));
    }

    public function getModuleInit(): string
    {
        $return = '';
        foreach ($this->layui_module as $module){
            $return .= "let {$module} = layui.{$module}\n";
        }
        return $return;
    }

    public function getCss(): string
    {
        return implode("\n", $this->css);
    }

    public function addCss(string $string): AccessManage
    {
        array_push($this->css, $string);
        return $this;
    }
}