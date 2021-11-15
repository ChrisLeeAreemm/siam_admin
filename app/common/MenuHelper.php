<?php


namespace app\common;

use app\model\ConfigsModel;
use think\helper\Str;

class MenuHelper
{
    public $auth_list = [];
    protected $order;

    /**
     * 列表转换树形结构
     * @param $list
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function list_to_tree($list)
    {
        $newList = [];
        foreach ($list as $t) {
            $newList[$t['auth_id']] = $t;
        }

        $this->auth_list = $newList;

        $configs     = ConfigsModel::where('config_name', 'auth_order')->field('config_value')->find();
        $order       = json_decode($configs['config_value'], TRUE);
        $this->order = $order;
        return $this;
    }

    /**
     * @return array
     */
    public function getTree()
    {
        return $this->AuthTree();
    }

    /**
     * @return array
     */
    public function getTreeRoles()
    {
        return $this->AuthTreeRules();
    }

    /**
     * @return array
     */
    public function getTreeAuthMenu()
    {
        return $this->AuthMenuTree();
    }

    /**
     * 权限列表数组
     * @param $order
     * @return array
     */
    private function AuthTree($order = '')
    {
        if (!$order) {
            $order = $this->order;
        }
        $return = [];
        foreach ($order as $value) {
            // 未有权限
            if (empty($this->auth_list[$value['id']])) {
                continue;
            }

            $tem = $this->auth_list[$value['id']];
            if (isset($value['child'])) {
                $tem['child'] = $this->AuthTree($value['child']);
            }
            $return[] = $tem;
        }
        return $return;
    }

    /**
     * 菜单的权限数组
     * @param $order
     * @return array
     */
    private function AuthMenuTree($order = '')
    {
        if (!$order) {
            $order = $this->order;
        }
        $return = [];
        foreach ($order as $value) {
            // 未有权限
            if (empty($this->auth_list[$value['id']])) {
                continue;
            }
            //拼接数组
            $tem          = $this->auth_list[$value['id']];
            $res = [];
            $res['title'] = $tem['auth_name'];
            $res['icon']  = $tem['auth_icon'];
            $res['href']  = $tem['auth_rules'];
            $res['target']  = '_self';
            //为空则是最高栏，无跳转
            if (!empty($res['href'])){
                if (!Str::startsWith($tem['auth_rules'], '/page') && !Str::startsWith($tem['auth_rules'], 'page') && !Str::startsWith($tem['auth_rules'], 'http')) {
                    $res['href'] = "/index.php/" . $tem['auth_rules'];
                    $res['href'] = str_replace("//", "/", $res['href']);
                }
            }
            
            if (isset($value['child'])) {
                $res['child'] = $this->AuthMenuTree($value['child']);
            }
            $return[] = $res;
        }
        return $return;
    }

    /**
     * 角色的权限列表
     * @param $order
     * @return array
     */
    private function AuthTreeRules($order = '',$pid = '0')
    {
        if (!$order) {
            $order = $this->order;
        }

        $return = [];
        foreach ($order as $value) {
            // 未有权限
            if (empty($this->auth_list[$value['id']])) {
                continue;
            }

            $tem          = $this->auth_list[$value['id']];
            $res['id']    = $tem['auth_id'];
            $res['title'] = $tem['auth_name'];
            $res['parentId'] = (string)$pid;
            $res['checkArr'] = json_encode(['type'=>0,'checked'=>0]);
            if (isset($value['child'])) {
                $res['children'] = $this->AuthTreeRules($value['child'],$res['id']);
            }
            $return[] = $res;
            unset($res);
        }
        return $return;
    }

    /**
     * 列表转化html
     */
    public function tree_to_html($list)
    {
        $html = '';
        foreach ($list as $key => $value) {
            if (!empty($value['child'])) {

                $html .= <<<html
<li class="layui-nav-item">
    <a href="javascript:;" lay-tips="{$value['auth_name']}"  lay-direction="2">
    <i class="layui-icon layui-icon-home"></i>
        <cite>{$value['auth_name']}</cite>
    </a>
    <dl class="layui-nav-child">
html;

                foreach ($value['child'] as $v) {
                    if (!empty($v['child'])) {
                        // 三级
                        $html .= <<<html
<dd>
    <a href="javascript:;">{$v['auth_name']}</a>
    <dl class="layui-nav-child">
html;
                        foreach ($v['child'] as $threev) {
                            $temUrl = url($threev['auth_rules']);
                            $html   .= <<<html
<dd><a lay-href="{$temUrl}">{$threev['auth_name']}</a></dd>
html;
                        }
                        $html .= "</dl>";
                        // 三级结束
                    } else {
                        $temUrl = url($v['auth_rules']);
                        $html   .= <<<html
<dd>
    <a lay-href="{$temUrl}">
    {$v['auth_name']}
    </a>
</dd>
html;
                    }
                }

                $html .= "</dl></li>";
                // 二级结束

            } else {
                // 一级的
                $temUrl = url("{$value['auth_rules']}");

                $html .= <<<html
<li data-name="{$value['auth_name']}" class="layui-nav-item">
    <a lay-href="{$temUrl}" lay-tips="{$value['auth_name']}" lay-direction="2">
        <i class="layui-icon layui-icon-home"></i>
        <cite>{$value['auth_name']}</cite>
    </a>
</li>
html;
            }
        }

        return $html;
    }
}
