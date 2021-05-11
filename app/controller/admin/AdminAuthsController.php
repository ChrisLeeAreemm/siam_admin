<?php

namespace app\controller\admin;

use app\common\MenuHelper;
use app\exception\ErrorCode;
use app\model\AuthsModel as Model;
use app\model\ConfigsModel;

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
        $tree        = $menu_helper->list_to_tree($list)->getTree();
        $html        = $this->makeTree($tree);
        return $this->send(ErrorCode::SUCCESS, ['lists' => $html]);

    }

    public function get_roles_tree()
    {
        $auths       = new Model();
        $list        = $auths->get_admin_auths_by_u_id(1);
        $menu_helper = new MenuHelper();
        $tree        = $menu_helper->list_to_tree($list)->getTreeRoles();
        return $this->send(ErrorCode::SUCCESS, ['tree' => $tree]);
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

        $configsModel = ConfigsModel::where('config_name', 'auth_order')->find();
        $res         = $configsModel->force()->save(['config_value' => $order]);
        if (!$res) {
            return $this::send(ErrorCode::DB_DATA_UPDATE_FAILE, [], 'ERROR');
        }
        return $this::send(ErrorCode::SUCCESS, [], 'SUCCESS');

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
        <a href="javascript:editAuth('{$value['auth_id']}');" title="编辑"><i class="layui-icon">&#xe642;</i></a>
        <a href="javascript:delAuth('{$value['auth_id']}');" title="删除"><i class="layui-icon">&#xe640;</i></a>
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
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], '获取失败');
        }
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result]);
    }

    /**
     * @return \think\response\Json
     */
    public function add()
    {
        $param = input();
        $param['create_time'] = $param['update_time'] = date('Y-m-d H:i:s');
        $res   = Model::create($param);
        if (!$res) {
            return $this::send(ErrorCode::DB_DATA_ADD_FAILE, [], 'ERROR');
        }
        // 如果是菜单还要更新排序
        $configs_info = ConfigsModel::where('config_name', 'auth_order')->find();
        $authOrder   = json_decode($configs_info['config_value'], true);
        $authOrder[] = [
            'id' => $res->auth_id
        ];
        $configsModel = ConfigsModel::where('config_name', 'auth_order')->find();
        $res         = $configsModel->force()->save(['config_value' => json_encode($authOrder)]);
        if (!$res) {
            return $this::send(ErrorCode::DB_DATA_UPDATE_FAILE, [], 'ERROR');
        }
        return $this::send(ErrorCode::SUCCESS, [], 'SUCCESS');

    }

    /**
     * @return \think\response\Json
     */
    public function edit()
    {
        $param = input();
        $param['update_time'] = date('Y-m-d H:i:s');
        $start = Model::find($param['auth_id']);
        $res   = $start->save($param);

        if (!$res) {
            return $this->send(ErrorCode::DB_DATA_UPDATE_FAILE, [], '编辑失败');

        }
        return $this->send(ErrorCode::SUCCESS,[],'成功');
    }

    /**
     * @return \think\response\Json
     */
    public function delete()
    {
        $id = input('auth_id');

        $result = Model::destroy($id);
        if (!$result){
            return $this->send(ErrorCode::DB_EXCEPTION, [], '失败');
        }
        return $this->send(ErrorCode::SUCCESS, [], '成功');

    }
}