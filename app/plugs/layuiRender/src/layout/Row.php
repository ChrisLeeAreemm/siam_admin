<?php


namespace app\plugs\layuiRender\src\layout;


class Row
{
    const head = '<div class="layui-row">';
    const foot = '</div>';
    private $cols = [];

    public function col(array $class_array, $display)
    {
        array_push($this->cols, [
            'class'   => $class_array,
            'display' => $display,
        ]);
        return $this;
    }

    public function render(): string
    {
        $return = static::head;

        foreach ($this->cols as $col){
            $class = '';
            foreach ($col['class'] as $class_set){
                $class .= 'layui-col-'.$class_set;
            }
            $return .= "<div class='{$class}'>";
            $return .= $col['display'];
            $return .= '</div>';
        }

        $return.=static::foot;
        return $return;
    }

    public function __toString()
    {
        return $this->render();
    }

}