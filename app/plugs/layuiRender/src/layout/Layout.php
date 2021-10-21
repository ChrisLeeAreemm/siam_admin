<?php


namespace app\plugs\layuiRender\src\layout;


use Siam\Component\Singleton;

class Layout
{
    private $rows = [];
    public static function make()
    {
        return new static;
    }

    public function addRow($build_row)
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
}