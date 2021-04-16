<?php

namespace app\plugs\dbHelper\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;
use think\facade\Db;

/**
 * 数据表字典
 * Class DbDictController
 * @package app\plugs\dbHelper\controller
 */
class DbDictController extends PlugsBaseController
{
    /**
     * 获取字典
     * @return \think\response\Json|void
     */
    public function get_list()
    {
        // 校验参数
        $this->validate(['table_name' => 'require'], $this->request->param());
        $table_name = $this->request->param('table_name');
        // 读取表详情
        try {
            $table_info = Db::table($table_name)->getFields($table_name);
        } catch (\Exception $exception) {
            return $this->send(ErrorCode::DB_EXCEPTION, [$exception->getLine()], $exception->getMessage());
        }

        if (!$table_info) {
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, []);
        }
        // 输出
        $list = [];
        foreach ($table_info as $key => $value) {
            $list[] = $value;
        }

        return $this->send(ErrorCode::SUCCESS, ['list' => $list], 'SUCCESS');
    }

    /**
     * 获取表
     * @return \think\response\Json|void
     */
    public function get_tables()
    {
        return (new DbTablesController($this->app))->get_list();
    }
}