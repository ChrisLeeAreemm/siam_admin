<?php

namespace app\plugs\exceptionLogger\controller;


use app\exception\ErrorCode;
use app\plugs\exceptionLogger\model\PlugsExceptionLoggerModel;
use app\plugs\PlugsBaseController;
use think\Exception;

class ExceptionLoggerController extends PlugsBaseController
{
    /**
     * 异常记录列表
     * @return \think\response\Json|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_list()
    {
        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = PlugsExceptionLoggerModel::page($page, $limit)->order('id', 'DESC')->field([
            'id',
            'exception_class',
            'exception_date',
            'create_time',
        ])->select();
        $count  = PlugsExceptionLoggerModel::count();
        return $this->send(ErrorCode::SUCCESS, ['list' => $result, 'count' => $count]);
    }

    /**
     * 还原异常
     * @return false|string|\think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check_exception()
    {
        $this->validate(['id' => 'require'], input());
        $id     = input('id');
        $result = PlugsExceptionLoggerModel::find($id);
        if (!$result) {
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], '数据不存在');
        }
        return $this->renderContent(json_decode($result->exception_data, true));
    }

    /**
     * 图表数据
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function echarts_data()
    {
        $exceptions = PlugsExceptionLoggerModel::select();
        if (!$exceptions){
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], '数据不存在');
        }
        //获取7天
        $day = [];
        $data = [];
        for ($i=0;$i<7;$i++){
            $day[] = date('Ymd',strtotime("-$i day"));
        }
        sort($day);
        foreach ($day as $v){
            $count[] = PlugsExceptionLoggerModel::where('exception_date',$v)->count();
        }
        $data['days'] = $day;
        $data['count'] = $count;

        return $this->send(ErrorCode::SUCCESS, $data,'SUCCESS');
    }

    /**
     * 渲染异常
     * @param $data
     * @return false|string
     */
    protected function renderContent($data)
    {
        ob_start();
        extract($data);
        include $this->app->config->get('app.exception_tmpl') ?: __DIR__ . '/../../tpl/think_exception.tpl';
        return ob_get_clean();
    }
}