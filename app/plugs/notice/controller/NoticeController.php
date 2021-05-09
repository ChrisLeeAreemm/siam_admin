<?php

namespace app\plugs\notice\controller;

use app\exception\ErrorCode;
use app\plugs\notice\model\PlugsNoticeModel;
use app\plugs\notice\model\PlugsNoticeReadModel;
use app\plugs\PlugsBaseController;


/**
 * 站内信
 * Class NoticeController
 * @package app\plugs\notice\controller
 */
class NoticeController extends PlugsBaseController
{
    public function get_list()
    {
        $page    = input('page', 1);
        $limit   = input('limit', 10);
        $where   = $this->build_where();
        $whereOr = $this->build_whereOr();
        $result  = PlugsNoticeModel::with(['users'])->where($where)->whereOr($whereOr)->page($page, $limit)->order('notice_id', 'desc')->select();
        $notice_ids   = [];

        //取出ID数组
        foreach ($result as $v) {
            $notice_ids[] = $v['notice_id'];
        }

        //对比取出已读数据
        $read_notice = PlugsNoticeReadModel::where('u_id', '=', $this->who->u_id)->whereIn('notice_id', implode(',', $notice_ids))->select();

        //释放
        unset($notice_ids);

        if ($read_notice->isEmpty()) { //空数据则全部未读
            foreach ($result as $v){
                $v['read_status'] = '未读';
            }
        }else{
            //有数据，进行区分
            foreach ($result as $v) {
                $v['read_status'] = '未读';
                foreach ($read_notice as $vo) {
                    if ($v['notice_id'] === $vo['notice_id']) {
                        $v['read_status'] = '已读';
                    }
                }
            }
        }

        //释放
        unset($read_notice);

        $count  = PlugsNoticeModel::where($where)->whereOr($whereOr)->count();
        return $this->send(ErrorCode::SUCCESS, ['lists' => $result, 'count' => $count]);
    }

    /**
     * 发送站内信
     * @return \think\response\Json
     * @throws \app\exception\AuthException
     */
    public function send_notice()
    {
        $this->validate(['notice_title' => 'require'], input());
        $data['notice_title']    = input('notice_title');
        $data['notice_content']  = input('notice_content');
        $data['notice_sender']   = $this->who->u_id;
        $data['notice_receiver'] = PlugsNoticeModel::NOTICE_RECEIVER_ALL;
        $data['notice_type']     = input('notice_type');
        if (input('select')){
            $data['notice_receiver'] = json_encode(explode(',',input('select')));
        }
        $data['create_time']     = $data['update_time'] = date('Y-m-d H:i:s');
        // 写入站内信表
        $notice = PlugsNoticeModel::create($data);
        if (!$notice){
            return $this->send(ErrorCode::DB_EXCEPTION,[],'NOTICE_CREATE_FAIL');
        }
        return $this->send(ErrorCode::SUCCESS,[],'SUCCESS');

    }

    /**
     * 阅读
     * @return \think\response\Json
     * @throws \app\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function read_notice()
    {
        $this->validate(['notice_id' => 'require'], input());
        $notice_id = input('notice_id');
        $notice    = PlugsNoticeModel::with(['users'])->find($notice_id);
        if (!$notice) {
            return $this->send(ErrorCode::DB_DATA_DOES_NOT_EXIST, [], 'NOTICE_DOES_NOT_EXIST');
        }
        //判断是否未读
        $where = [
            'u_id'      => $this->who->u_id,
            'notice_id' => $notice_id
        ];
        $is_read = PlugsNoticeReadModel::where($where)->find();
        //未读，写入已读表
        if (!$is_read){
            $data = [
                'u_id'        => $this->who->u_id,
                'notice_id'   => $notice_id,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ];
            $readModel = new PlugsNoticeReadModel();
            $save = $readModel->save($data);
            if (!$save) {
                return $this->send(ErrorCode::DB_EXCEPTION, [], 'SAVE_READ_FAIL');
            }
        }

        return $this->send(ErrorCode::SUCCESS, ['notice'=>$notice], 'SUCCESS');

    }

    private function build_where($where = [])
    {
        $where['notice_receiver'] = PlugsNoticeModel::NOTICE_RECEIVER_ALL;
        return $where;
    }

    private function build_whereOr($whereOr = [])
    {
        $whereOr[] = ['notice_receiver', 'like', "%\"{$this->who->u_id}\"%"];
        return $whereOr;
    }

}