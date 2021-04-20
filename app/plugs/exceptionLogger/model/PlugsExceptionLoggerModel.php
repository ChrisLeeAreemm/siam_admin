<?php
/**
 * Created by CurdPlugs
 * User: Chris
 */

namespace app\plugs\exceptionLogger\model;

use app\model\BaseModel;
use think\Model;

#start
/**
 * bar_plugs_exception_logger
 * @property mixed id                 自增ID
 * @property mixed exception_class    异常类名(异常类型)
 * @property mixed exception_date     异常日期
 * @property mixed exception_data     异常内容
 * @property mixed create_time        创建时间
 */
#end
class PlugsExceptionLoggerModel extends BaseModel
{
    protected $name = 'plugs_exception_logger';
    protected $pk   = 'id';
}