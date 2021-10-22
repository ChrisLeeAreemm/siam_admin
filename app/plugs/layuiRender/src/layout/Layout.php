<?php


namespace app\plugs\layuiRender\src\layout;


use app\plugs\layuiRender\src\BaseDom;

class Layout extends BaseDom
{
    private $rows = [];
    public static function make(): Layout
    {
        return new static;
    }

    public function addRow($build_row): Layout
    {
        $row = new Row;
        array_push($this->rows, $row);
        call_user_func($build_row, $row);
        return $this;
    }

    public function __toString()
    {
        $return = "";
        foreach ($this->rows as $row){
            $return .= $row;
        }
        return $return;
    }

    function head(): string
    {
        return '';
    }

    function foot(): string
    {
        return '';
    }
}