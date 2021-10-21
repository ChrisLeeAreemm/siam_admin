<?php


namespace app\plugs\layuiRender\test;


use app\plugs\layuiRender\src\Grid;
use app\plugs\layuiRender\src\layout\Layout;
use app\plugs\layuiRender\src\layout\Row;
use app\plugs\layuiRender\src\LayuiTemplet;
use app\plugs\layuiRender\src\panel\Panel;
use app\plugs\layuiRender\src\table\Table;

class Test
{
    public function run()
    {
        $ok = LayuiTemplet::make()->body(Layout::make()
            ->addRow(function(Row $row){
                $row->col(['xs6'], Panel::make()->title('测试卡片')->content("内容测试<br/>ok"))
                    ->col(['xs6'], Table::make()->url('ok')->id('user_info')->grid(new Grid(1)));
            }));
        echo $ok;
    }
}
