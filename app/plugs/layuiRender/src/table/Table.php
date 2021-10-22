<?php


namespace app\plugs\layuiRender\src\table;


use app\plugs\layuiRender\src\AccessManage;
use app\plugs\layuiRender\src\BaseDom;
use app\plugs\layuiRender\src\Grid;

class Table extends BaseDom
{
    private $id;
    private $url;
    /**
     * @var Grid
     */
    private $grid;
    private $where;

    public static function make(): Table
    {
        return new static;
    }
    public function id($id): Table
    {
        $this->id = $id;
        return $this;
    }
    public function url($url): Table
    {
        $this->url = $url;
        return $this;
    }
    public function grid(Grid $grid): Table
    {
        $this->grid = $grid;
        return $this;
    }

    public function where($js):Table
    {
        $this->where = $js;
        return $this;
    }

    public function render(): string
    {
        // grid is required;
        if (!$this->grid){
            return "\nerror:grid is required;\n";
        }

        // 优化
        $return = $this->head().$this->foot().$this->grid->getTableAttachmentHtml();

        $js = <<<js
table.render({
    elem: '#{$this->id}'
    ,height: 312
    ,url: '{$this->url}'
    ,page: true
    ,where: where()
    ,cols: [[
      {$this->grid->__toTableString()}
    ]]// feature 二级表头
    , response: {
        statusCode: 200
    }
    , parseData: function(res){
        return {
            "code": res.code,
            "msg": res.msg,
            "count": res.data.count,
            "data": res.data.lists
        };
    }
})
js;
        AccessManage::getInstance()->addJs(<<<js
function where(){
    return {
        access_token: layui.data(setter.tableName)[setter.request.tokenName]
        {$this->where}
    };
}
js
,'setter');
        AccessManage::getInstance()->addJs($js,'table');

        return $return;

    }

    public function __toString()
    {
     return $this->render();
    }

    function head(): string
    {
        return "<table id='{$this->id}' lay-filter='{$this->id}'>";
    }

    function foot(): string
    {
        return "</table>";
    }
}