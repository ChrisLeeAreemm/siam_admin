<?php


namespace app\plugs\layuiRender\src;



class Grid
{
    private $table_attachment_html;
    private $builder;
    private $columns = [];

    private function __construct($callable)
    {
        $this->builder = $callable;
    }

    public static function make($callable): Grid
    {
        $grid = new static($callable);
        call_user_func($grid->builder, $grid);
        return $grid;
    }

    public function column($name, $label = ''): Column
    {
        $column = new Column();
        $column->setName($name);
        $column->setLabel($label);
        $column->setGrid($this);

        array_push($this->columns, $column);
        return $column;
    }

    public function hideAddButton()
    {

    }

    public function __toTableString(): string
    {
        $return = [];
        /** @var \app\plugs\layuiRender\src\Column $column */
        foreach ($this->columns as $column ){
            $return[] = $column->__toTableString();
        }
        return implode("\n,", $return);
    }



    // table组件使用的附加html代码
    // 工具html代码
    /**
     * @return mixed
     */
    public function getTableAttachmentHtml()
    {
        return $this->table_attachment_html;
    }

    /**
     * @param mixed $table_attachment_html
     */
    public function setTableAttachmentHtml($table_attachment_html): void
    {
        $this->table_attachment_html = $table_attachment_html;
    }




    // 转为新增/编辑时的表单代码
    public function __toFormString(){

    }
}