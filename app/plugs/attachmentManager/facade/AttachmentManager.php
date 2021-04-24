<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/4/24
 * Time: 18:00
 */

namespace app\plugs\attachmentManager\facade;


use app\plugs\attachmentManager\driver\AbstractDriver;

class AttachmentManager
{
    /** @var AbstractDriver */
    protected $driver;

    public function __construct()
    {
        // 读取配置 决定上传的驱动是什么
        $this->driver = null;

    }

    function upload()
    {

        $this->driver->upload();
        // 自动维护数据库 插入数据
    }

    function delete()
    {
        $this->driver->delete();
        // 自动维护数据库 删除数据
    }
}