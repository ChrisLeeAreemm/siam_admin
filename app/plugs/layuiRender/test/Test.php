<?php


namespace app\plugs\layuiRender\test;


use app\plugs\layuiRender\src\AccessManage;
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
        // 全局css AccessManage::getInstance()->addCss("body{background:red;}");
        return LayuiTemplet::make()->body(
            Layout::make()
                ->addRow(function(Row $row){
                    $table = Table::make()->url('http://127.0.0.1:8000/index.php/admin/users/get_list')
                        ->where(",test:123,test2:$('#id').val()")
                        ->id('user_info')
                        ->grid($this->grid());
                    $row
                        ->setClass('layui-col-space20')
                        ->col(['xs6'], Panel::make()->title('会员列表')->content("1111<br/>ok"))
                        ->col(['xs6'], Panel::make()->title('测试卡片')->content($table));
                })
        );
    }

    public function grid(): Grid
    {
        return Grid::make(function(Grid  $grid){
            $grid->column('u_id', '用户id');
            $grid->column('u_name', '用户名');
            $grid->column('right', '操作')->setFixed('right')->setToolbar("#barDemo", <<<html
<script type="text/html" id="barDemo">
  <a class="layui-btn layui-btn-xs" lay-event="edit">编辑</a>
  <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del">删除</a>
</script>
html
);
        });
    }
}
