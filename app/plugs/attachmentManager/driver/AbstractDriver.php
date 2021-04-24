<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/24
 * Time: 17:59
 */

namespace app\plugs\attachmentManager\driver;


abstract class AbstractDriver
{
    abstract function upload();
    abstract function delete();
}