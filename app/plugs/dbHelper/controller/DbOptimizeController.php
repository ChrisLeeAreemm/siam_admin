<?php

namespace app\plugs\dbHelper\controller;


use app\exception\ErrorCode;
use app\plugs\PlugsBaseController;
use think\Exception;
use think\facade\Db;

class DbOptimizeController extends PlugsBaseController
{
    /**
     * 获取表状态
     * @return \think\response\Json|void
     */
    public function get_list()
    {
        $prefix = config('database.connections.mysql.prefix');
        if (!$prefix){
            return $this->send(ErrorCode::PARAM_FORMAT_ERROR,[],'请设置数据库前缀');
        }
        $sql = "SHOW TABLE STATUS LIKE '$prefix%'";
        try {
            $result = Db::query($sql);
            foreach ($result as &$value){
                $value['Data_length'] = $this->get_size($value['Data_length']);
                $value['Index_length'] = $this->get_size($value['Index_length']);
                $value['Data_free'] = $this->get_size($value['Data_free']);
            }
            $count = count($result);
        }catch (\Exception $exception){
            return $this->send(ErrorCode::DB_EXCEPTION,[],$exception->getMessage());
        }
        if (!$result){
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST,[],'无数据');
        }
        
        return $this->send(ErrorCode::SUCCESS,['list'=>$result,'count'=>$count],'SUCCESS');
    }
    
    /**
     * 优化表
     * @return \think\response\Json
     */
    public function perform_optimize()
    {
        $this->validate(['data'=>'require'],input());
        $params = json_decode(input('data'),true);
        if (!$params){
            return $this->send(ErrorCode::PARAM_EMPTY,[],'无数据');
        }
        foreach ($params as $value){
            $sql = "OPTIMIZE TABLE {$value['Name']}";
            try {
                Db::query($sql);
            }catch (\Exception $exception){
                return $this->send(ErrorCode::DB_EXCEPTION,[],$exception->getMessage());
            }
        }
        return $this->send(ErrorCode::SUCCESS,[],'优化成功');
    }
    
    /**
     * 字节大小转换
     * @param $num
     * @return string
     */
    protected function get_size($num) {
        $p = 0;
        $format = 'bytes';
        if( $num > 0 && $num < 1024 ) {
            $p = 0;
            return number_format($num) . ' ' . $format;
        }
        if( $num >= 1024 && $num < pow(1024, 2) ){
            $p = 1;
            $format = 'KB';
        }
        if ( $num >= pow(1024, 2) && $num < pow(1024, 3) ) {
            $p = 2;
            $format = 'MB';
        }
        if ( $num >= pow(1024, 3) && $num < pow(1024, 4) ) {
            $p = 3;
            $format = 'GB';
        }
        if ( $num >= pow(1024, 4) && $num < pow(1024, 5) ) {
            $p = 3;
            $format = 'TB';
        }
        $num /= pow(1024, $p);
        return number_format($num, 3) . ' ' . $format;
    }
}