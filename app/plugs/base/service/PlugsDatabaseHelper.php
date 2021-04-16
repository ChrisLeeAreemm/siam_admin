<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/16
 * Time: 20:12
 */

namespace app\plugs\base\service;


use EasySwoole\DDL\DDLBuilder;
use think\facade\Db;

class PlugsDatabaseHelper
{
    public static function create_ddl($table, callable $callable){
        return DDLBuilder::create(env('database.prefix','').$table, $callable);
    }

    public static function run($sql)
    {
        $sql = (string) $sql;
        $res = Db::query($sql);
        return $res;
    }
}