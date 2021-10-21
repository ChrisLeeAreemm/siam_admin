<?php


namespace app\plugs\layuiRender\src;


class Grid
{
    private $builder;
    private $columns = [];
    public function __construct($callable)
    {
        $this->builder = $callable;
    }

    public function column($name, $label)
    {

    }

    public function hideAddButton()
    {

    }

    public function test(){
        // 生成lists列表
        $test = new Grid(function(Grid $grid){
           $grid->column('test', 'ok');
        });
    }

    public function __toTableString(){
        return <<<html
{field: 'id', title: 'ID', width:80, sort: true, fixed: 'left'}
,{field: 'username', title: '用户名', width:80}
,{field: 'sex', title: '性别', width:80, sort: true}
,{field: 'city', title: '城市', width:80} 
,{field: 'sign', title: '签名', width: 177}
,{field: 'experience', title: '积分', width: 80, sort: true}
,{field: 'score', title: '评分', width: 80, sort: true}
,{field: 'classify', title: '职业', width: 80}
,{field: 'wealth', title: '财富', width: 135, sort: true}
html;

    }
}