<?php


namespace app\plugs\cms\controller;


use think\facade\View;

class CmsDefaultTemplateController
{
    public function index()
    {
        return View::fetch('/plugs/cms/template/default/personal');
    }
}