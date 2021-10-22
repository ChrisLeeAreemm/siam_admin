<?php


namespace app\plugs\layuiRender\src\layout;


use app\plugs\layuiRender\src\BaseDom;

class Row extends BaseDom
{
    private $cols = [];

    public function head(): string
    {
        return "<div class='layui-row {$this->getClass()}' {$this->getData()}>\n";
    }
    public function foot():string
    {
        return "\n</div>";
    }

    public function col(array $class_array, $display): Row
    {
        array_push($this->cols, [
            'class'   => $class_array,
            'display' => $display,
        ]);
        return $this;
    }

    public function render(): string
    {
        $return = $this->head();
        foreach ($this->cols as $col){
            $class = '';
            foreach ($col['class'] as $class_set){
                $class .= 'layui-col-'.$class_set;
            }
            $return .= "<div class='{$class}'>\n";
            $return .= $col['display'];
            $return .= "\n</div>";
        }

        $return .= $this->foot();
        return $return;
    }

    public function __toString()
    {
        return $this->render();
    }

}