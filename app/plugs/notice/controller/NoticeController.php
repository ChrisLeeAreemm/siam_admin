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
        foreach ($result as $v){
            //检查阅读记录
            $where_s = [
                'u_id'      => $this->who->u_id,
                'notice_id' => $v['notice_id']
            ];
            $is_read = PlugsNoticeReadModel::where($where_s)->find();
            $v['read_status'] = '已读';
            if (!$is_read) {
                $v['read_status'] = '未读';
            }
        }
        $count  = PlugsNoticeModel::count();
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
        $data['notice_receiver'] = '0';
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
        return $this->send(ErrorCode::SUCCESS, $notice, 'SUCCESS');

    }
    
    /**
     * 获取未读数量
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_unread_count()
    {
        $where   = $this->build_where();
        $whereOr = $this->build_whereOr();
        $result  = PlugsNoticeModel::where($where)->whereOr($whereOr)->select();
        $count   = 0;
        foreach ($result as $v){
            //未读数量
            $where_s = [
                'u_id'      => $this->who->u_id,
                'notice_id' => $v['notice_id']
            ];
            $is_read = PlugsNoticeReadModel::where($where_s)->find();
            if (!$is_read) {
                $count++;
            }
        }
        return $this->send(ErrorCode::SUCCESS, ['count'=>$count]);
    }

    private function build_where($where = [])
    {
        $where['notice_receiver'] = 0;
        return $where;
    }

    private function build_whereOr($whereOr = [])
    {
        $whereOr[] = ['notice_receiver', 'like', "%\"{$this->who->u_id}\"%"];
        return $whereOr;
    }

}