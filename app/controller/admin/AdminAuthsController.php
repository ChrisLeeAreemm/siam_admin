<?php

namespace app\controller\admin;

use app\model\AuthsModel as Model;

class AdminAuthsController extends AdminBaseController
{
    /**
     * @return mixed
     * @throws \think\Exception
     */
    public function get_list()
    {

        $page  = input('page', 1);
        $limit = input('limit', 10);

        $result = Model::paginate(['page' => $page, 'list_rows' => $limit,])->toArray();
        $lists  = $result['data'];
        $count  = $result['total'];
        return json(['code'=>'200','data'=>['lists'=>$lists,'count'=>$count],'msg'=>'']);

    }

    public function get_one()
    {
        $id = input('auth_id');
        $result = Model::find($id);
        if (!$result){
            return json(['code'=>'500','data'=>'','msg'=>'获取失败']);
        }
        return json(['code'=>'200','data'=>['lists'=>$result],'msg'=>'']);
    }

    /**
     * @return false|string
     */
    public function add()
    {

        $param = input();

        $start = Model::create($param);

        if (!$start) {
             return json(['code'=>'500','data'=>'','msg'=>'新增失败']);
        }
            return json(['code'=>'200']);
    }

    /**
     * @return false|string
     */
    public function edit()
    {
        $param = input();
        $start = Model::find($param['auth_id']);
        $res   = $start->save($param);

        if (!$res){
          return json(['code'=>'500','data'=>'','msg'=>'编辑失败']);
        }
        return json(['code'=>'200']);
    }

    /**
     * @return false|string
     */
    public function delete()
    {
        $id = input('auth_id');

        $result = Model::destroy($id);

        return json(['code'=>'200','msg'=>'ok']);
    }
}