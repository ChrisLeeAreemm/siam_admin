<?php


namespace app\plugs\layuiRender\src;


use think\facade\View;

class LayuiTemplet
{
    private $bodys = [];
    private $title = 'Siam Layui Render Plugs';
    private $meta = [];

    public static function make(): LayuiTemplet
    {
        return new static;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return \app\plugs\layuiRender\src\LayuiTemplet
     */
    public function setTitle(string $title): LayuiTemplet
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     * @return \app\plugs\layuiRender\src\LayuiTemplet
     */
    public function setMeta(array $meta): LayuiTemplet
    {
        $this->meta = $meta;
        return $this;
    }

    public function body($content): LayuiTemplet
    {
        array_push($this->bodys, $content);
        return $this;
    }

    public function render(): string
    {
        $basic = file_get_contents(__DIR__."/tpl/basic.html");

        $body =  "";
        foreach ($this->bodys as $body_one){
            $body .= $body_one;
        }

        $js                = AccessManage::getInstance()->renderJs();
        $css_file          = AccessManage::getInstance()->getCssFile();
        $js_file           = AccessManage::getInstance()->getJsFile();
        $layui_module      = AccessManage::getInstance()->getModule();
        $layui_module_init = AccessManage::getInstance()->getModuleInit();

        return View::display($basic, [
            'title'             => $this->title,
            'css_file'          => $css_file,
            'style'             => AccessManage::getInstance()->getCss(),
            'body'              => $body,
            'js'                => $js,
            'js_file'           => $js_file,
            'layui_module'      => $layui_module,
            'layui_module_init' => $layui_module_init,
            'meta'              => implode("\n", $this->meta),
        ]);

    }
    public function __toString()
    {
        return $this->render();
    }
}