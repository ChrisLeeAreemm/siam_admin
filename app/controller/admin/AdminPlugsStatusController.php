<?php

namespace app\controller\admin;

use app\exception\ErrorCode;
use app\model\PlugsStatusModel as Model;
use think\helper\Str;

class AdminPlugsStatusController extends AdminBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {

        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = Model::page($page, $limit)->select();
        $count  = Model::count();
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'count' => $count]);


    }

    public function get_one()
    {
        $this->validate(['plugs_name' => 'require'], input());
        $plugs_name = input('plugs_name');
        $result     = Model::find($plugs_name);
        if (!$result) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '获取失败');
        }
        //获取module

        $Plugs   = '\app\plugs\\' . $plugs_name . '\\' . 'Plugs';
        $plugs   = new $Plugs();
        $res_arr = $plugs->get_config()->getHandleModule();
        $arr     = ['admin', 'index', 'agent', 'notify'];
        foreach ($res_arr as $v) {
            $modules[] = [
                'name'     => $v,
                'value'    => $v,
                'selected' => true,
            ];
            foreach ($arr as $key => $value) {
                if ($value == $v) {
                    unset($arr[$key]);
                }
            }
        }
        //添加剩余的的module
        foreach ($arr as $value) {
            $arrs = [
                'name'  => $value,
                'value' => $value,
            ];
            array_push($modules, $arrs);
        }

        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'modules' => $modules]);
    }

    /**
     * 获取插件启动状态
     * @return \think\response\Json
     * @throws \app\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function check_status()
    {
        $this->validate(['plugs_name' => 'require'], input());
        $plugs_name = input('plugs_name');
        $result     = Model::field('plugs_status')->find($plugs_name);
        if (!$result) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '获取失败');
        }
        return $this->send(ErrorCode::SUCCESS, $result);
    }

    /**
     * @return \think\response\Json
     */
    public function add()
    {

        $param = input();

        $start = Model::create($param);

        if (!$start) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '新增失败');
        }
        return $this->send(ErrorCode::SUCCESS);
    }

    /**
     * @return \think\response\Json
     */
    public function edit()
    {
        $param = input();
        $start = Model::find($param['plugs_name']);
        $res   = $start->save($param);

        if (!$res) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '编辑失败');

        }
        return $this->send(ErrorCode::SUCCESS);
    }

    /**
     * @return \think\response\Json
     */
    public function delete()
    {
        $id = input('plugs_name');

        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS, [], 'ok');

    }
}