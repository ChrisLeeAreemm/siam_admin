<?php

namespace app\controller\admin;

use app\common\MenuHelper;
use app\exception\ErrorCode;
use app\model\AuthsModel as Model;
use app\model\SystemModel;

class AdminAuthsController extends AdminBaseController
{

    /**
     * @return \think\response\Json
     */
    public function get_list()
    {
        $auths       = new Model();
        $list        = $auths->get_admin_auths_by_u_id(1);
        $menu_helper = new MenuHelper();
        $tree        = $menu_helper->list_to_tree($list);
        $html        = $this->makeTree($tree);
        return $this->send(ErrorCode::SUCCESS, ['lists' => $html]);

    }

    /**
     * 更新菜单排序
     */
    function order_update()
    {
        $roleOrder = input('roleOrder');

        if (empty($roleOrder)) return $this::send(ErrorCode::PARAM_EMPTY, [], 'PARAMETERS_INVALID');

        // 字符替换
        $order = str_replace('children', 'child', $roleOrder);

        $SystemModel = SystemModel::find(1);
        $res         = $SystemModel->force()->save(['auth_order' => $order]);
        if ($res) {
            return $this::send(ErrorCode::SUCCESS, [], 'SUCCESS');
        }
        return $this::send(ErrorCode::DB_EXCEPTION, [], 'ERROR');
    }


    /**
     * 【辅助封装】数组转权限编辑可拖拽html
     * @param array $array
     * @return string
     */
    protected function makeTree(array $array)
    {
        if (empty($array) || !is_array($array)) return '';
        $html = '<ol class="dd-list">';
        foreach ($array as $key => $value) {
            $html .= <<<html
<li class="dd-item" data-id="{$value['auth_id']}">
    <div class="dd-handle">{$value['auth_name']} </div>
    <div class="dd-btn" style="position: absolute;right: 5px;top: 7px;">
        <a href="javascript:ediAuth('{$value['auth_id']}');">编辑</a>
    </div>
html;

            // 如果还有下级
            if (isset($value['child'])) {
                $html .= $this->makeTree($value['child']);
            }
            $html .= "</li>";
        }
        $html .= "</ol>";
        return $html;
    }

    public function get_one()
    {
        $id     = input('auth_id');
        $result = Model::find($id);
        if (!$result) {
            return $this->send(ErrorCode::THIRD_PART_ERROR, [], '获取失败');
        }
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result]);
    }

    /**
     * @return \think\response\Json
     */
    public function add()
    {

        $model = new Model();
        $res   = $model->save([
            'auth_name'   => $this->request->param('auth_name'),
            'auth_rules'  => $this->request->param('auth_rules'),
            'auth_type'   => $this->request->param('auth_type'),
            'create_time' => time(),
            'update_time' => time(),
        ]);

        if (!$res) {
            return $this::send(ErrorCode::DB_EXCEPTION, [], 'ERROR');
        }
        // 如果是菜单还要更新排序
        $system_info = SystemModel::find(['id' => 1])->toArray();
        $authOrder   = json_decode($system_info['auth_order'], true);
        $authOrder[] = [
            'id' => $model->auth_id
        ];
        $SystemModel = SystemModel::find(1);
        $res         = $SystemModel->force()->save(['auth_order' => json_encode($authOrder)]);
        if (!$res) {
            return $this::send(ErrorCode::DB_EXCEPTION, [], 'ERROR');
        }
        return $this::send(ErrorCode::SUCCESS, [], 'SUCCESS');

    }

    /**
     * @return \think\response\Json
     */
    public function edit()
    {
        $param = input();
        $start = Model::find($param['auth_id']);
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
        $id = input('auth_id');

        $result = Model::destroy($id);

        return $this->send(ErrorCode::SUCCESS, [], 'ok');

    }
}