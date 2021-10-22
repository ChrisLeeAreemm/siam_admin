<?php


namespace app\plugs\layuiRender\src;


class Column
{
    // 来源 对象关联
    /**
     * @var \app\plugs\layuiRender\src\Grid
     */
    private $grid;

    // 数据表格用
    private $width;
    private $minWidth = 60;
    private $name;
    private $label;
    private $sort;
    private $fixed;
    private $type="normal";
    private $hide = false;
    private $totalRow = false;
    private $colspan = 1 ;
    private $rowspan = 1;
    private $templet;
    private $toolbar;

    // 表单用 判断是什么类型的输入框
    private $dom;

    /**
     * @return Grid
     */
    public function getGrid(): Grid
    {
        return $this->grid;
    }

    /**
     * @param mixed $grid
     */
    public function setGrid(Grid $grid): Column
    {
        $this->grid = $grid;
        return $this;
    }



    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width): Column
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinWidth(): int
    {
        return $this->minWidth;
    }

    /**
     * @param $minWidth
     * @return \app\plugs\layuiRender\src\Column
     */
    public function setMinWidth($minWidth): Column
    {
        $this->minWidth = $minWidth;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): Column
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label): Column
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort): Column
    {
        $this->sort = $sort;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFixed()
    {
        return $this->fixed;
    }

    /**
     * @param mixed $fixed
     */
    public function setFixed($fixed): Column
    {
        $this->fixed = $fixed;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return \app\plugs\layuiRender\src\Column
     */
    public function setType(string $type): Column
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHide(): bool
    {
        return $this->hide;
    }

    /**
     * @param bool $hide
     * @return \app\plugs\layuiRender\src\Column
     */
    public function setHide(bool $hide): Column
    {
        $this->hide = $hide;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTotalRow(): bool
    {
        return $this->totalRow;
    }

    /**
     * @param  $totalRow
     * @return \app\plugs\layuiRender\src\Column
     */
    public function setTotalRow( $totalRow): Column
    {
        $this->totalRow = $totalRow;
        return $this;
    }

    /**
     * @return int
     */
    public function getColspan(): int
    {
        return $this->colspan;
    }

    /**
     * @param int $colspan
     * @return \app\plugs\layuiRender\src\Column
     */
    public function setColspan(int $colspan): Column
    {
        $this->colspan = $colspan;
        return $this;
    }

    /**
     * @return int
     */
    public function getRowspan(): int
    {
        return $this->rowspan;
    }

    /**
     * @param int $rowspan
     * @return \app\plugs\layuiRender\src\Column
     */
    public function setRowspan(int $rowspan): Column
    {
        $this->rowspan = $rowspan;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemplet()
    {
        return $this->templet;
    }

    /**
     * @param mixed $templet
     */
    public function setTemplet($templet): Column
    {
        $this->templet = $templet;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToolbar()
    {
        return $this->toolbar;
    }

    /**
     * @param mixed $toolbar
     */
    public function setToolbar($toolbar, $attachment): Column
    {
        $this->toolbar = $toolbar;
        // 加入到table的html中
        $this->grid->setTableAttachmentHtml($attachment);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * @param mixed $dom
     */
    public function setDom($dom): Column
    {
        $this->dom = $dom;
        return $this;
    }

    public function __toTableString(): string
    {
        $alias = [
            'name'  => 'field',
            'label' => 'title',
        ];;
        $black = [
            'dom', 'grid'
        ];

        $return = [];
        foreach ($this as $key => $value){
            if (is_null($value) || !$value) continue;
            if (in_array($key, $black)) continue;
            if (isset($alias[$key])) $key = $alias[$key];

            $return[] = "{$key} : {$this->__parseValue($value)}";
        }
        return "{".implode("\n,",$return)."}";
    }

    private function __parseValue($value)
    {
        if (is_numeric($value)){
            return $value;
        }else if (is_string($value)){
            return "'$value'";
        }
        return $value;
    }



}