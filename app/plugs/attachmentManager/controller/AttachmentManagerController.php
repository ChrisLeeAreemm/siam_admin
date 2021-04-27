<?php

namespace app\plugs\attachmentManager\controller;


use app\exception\ErrorCode;
use app\exception\ServiceException;
use app\plugs\attachmentManager\model\PlugsAttachmentConfigModel;
use app\plugs\attachmentManager\model\PlugsAttachmentListModel;
use app\plugs\attachmentManager\service\AttachmentService;
use app\plugs\PlugsBaseController;

class AttachmentManagerController extends PlugsBaseController
{
    /**
     * @return \think\response\Json
     * @throws \Exception
     */
    public function save_config()
    {
        $data = input();

        $list = [];
        foreach ($data as $config_name => $config_value){
            if ($config_name === 'access_token') continue;
            $list[] = [
                'key' => $config_name,
                'value' => $config_value,
            ];
        }
        (new PlugsAttachmentConfigModel)->saveAll($list, true);

        return $this->send(ErrorCode::SUCCESS, [
        ], 'SUCCESS');
    }

    /**
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function get_config()
    {
        $list = PlugsAttachmentConfigModel::select();

        return $this->send(ErrorCode::SUCCESS, [
            'list' => $list
        ], 'SUCCESS');
    }

    /**
     * @return \think\response\Json
     * @throws \app\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function upload()
    {
        $user = $this->auth();

        $file   = request()->file('file');
        try {
            $upload = AttachmentService::upload($file, $user);
        } catch (ServiceException $e) {
            return $this->send(ErrorCode::FILE_WRITE_FAIL, [
                'exception' => $e->getMessage()
            ], 'FAIL');
        }

        return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
    }

    /**
     * @return \think\response\Json|void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \app\exception\AuthException
     */
    public function get_list()
    {
        // 根据u_id决定返回的列表
        $user = $this->auth();
        $where = [];
        if ($user->u_id!==1){
            $where = [
                'u_id' => $user->u_id,
            ];
        }
        $list  = PlugsAttachmentListModel::page(input('page',1), input('limit', 16))->where($where)->select();
        $count = PlugsAttachmentListModel::where($where)->count();

        // 把压缩包、txt等文件的图片 替换成固定图片
        $auto = [
            'txt' => 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=2078691400,3777721492&fm=26&gp=0.jpg',
            'zip' => 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=1154197189,3034638507&fm=26&gp=0.jpg',
            'rar' => 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=2194979228,194539310&fm=26&gp=0.jpg',
            '7z' => 'https://ss0.bdstatic.com/70cFuHSh_Q1YnxGkpoWK1HF6hhy/it/u=3455925441,2926567079&fm=26&gp=0.jpg',
            'exe' => 'https://ss2.bdstatic.com/70cFvnSh_Q1YnxGkpoWK1HF6hhy/it/u=1719543084,3547497348&fm=26&gp=0.jpg',
        ];
        /**
         * @var  $key
         * @var PlugsAttachmentListModel $value
         */
        foreach ($list as $key => $value){
            if (isset($auto[$value->file_type])){
                $list[$key]['cover'] = $auto[$value->file_type];
            }else{
                $list[$key]['cover'] = $value['real_path'];
            }
        }
        return $this->send(ErrorCode::SUCCESS, [
            'lists' => $list,
            'count' =>$count,
            'base_url' => '',
        ], 'SUCCESS');
    }

    /**
     * @return \think\response\Json|void
     * @throws \app\exception\AuthException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function delete()
    {
        $this->validate([
            'id' => 'require'
        ]);
        $this->auth();

        $upload = PlugsAttachmentListModel::find(input('id'));
        $this->validate([
            'upload' => 'require'
        ], [
            'upload' => $upload
        ]);

        AttachmentService::delete($upload);

        return $this->send(ErrorCode::SUCCESS, [], 'SUCCESS');
    }

}