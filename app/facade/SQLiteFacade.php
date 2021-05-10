<?php


namespace app\facade;

use \think\db\connector\Sqlite;
use think\db\Query;
use think\DbManager;
use think\facade\Db;

// $sqlite  = SQLiteFacade::connect();
// $builder = SQLiteFacade::builder($sqlite);
//
// $res = $builder->table('test')->insert([
//     'ID' => 2,
//     'NAME' => 'SIAM',
//     'AGE' => 23,
// ]);
//
// dump($res);

class SQLiteFacade
{
    public static function connect($database = 'siam.sqlite')
    {
        return Db::connect($database);
    }

    public static function builder( $connect)
    {
        return (new Query($connect));
    }
}