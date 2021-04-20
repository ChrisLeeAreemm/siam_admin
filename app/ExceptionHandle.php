<?php

namespace app;

use app\exception\BaseException;
use app\model\PlugsStatusModel;
use app\plugs\base\service\PlugsBaseHelper;
use app\plugs\exceptionLogger\model\PlugsExceptionLoggerModel;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable      $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        // 是否安装异常记录插件，是则插入
        if (PlugsBaseHelper::isInstall("exceptionLogger")) {
            $plugs_status = PlugsBaseHelper::getPlugsStatus("exceptionLogger");
            if ($plugs_status['plugs_status'] == PlugsStatusModel::PLUGS_STATUS_ON) {
                //序列化保存
                $ExceptionLogger                  = new PlugsExceptionLoggerModel();
                $ExceptionLogger->exception_class = get_class($e);
                $ExceptionLogger->exception_date  = date('Ymd');
                $traces[] = [
                    'name'    => get_class($e),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'code'    => $this->getCode($e),
                    'message' => $this->getMessage($e),
                    'trace'   => $e->getTrace(),
                    'source'  => $this->getSourceCode($e),
                ];
                $data = [
                    'code'    => $this->getCode($e),
                    'message' => $this->getMessage($e),
                    'traces'  => $traces,
                    'datas'   => $this->getExtendData($e),
                    'tables'  => [
                        'GET Data'            => $this->app->request->get(),
                        'POST Data'           => $this->app->request->post(),
                        'Files'               => $this->app->request->file(),
                        'Cookies'             => $this->app->request->cookie(),
                        'Session'             => $this->app->exists('session') ? $this->app->session->all() : [],
                        'Server/Request Data' => $this->app->request->server(),
                    ],
                ];
                $ExceptionLogger->exception_data  = json_encode($data);
                $ExceptionLogger->create_time     = date('Y-m-d H:i:s');
                $ExceptionLogger->save();
            }
        }


        if ($e instanceof BaseException) {
            return json([
                'code' => $e->getCode(),
                'data' => $e->get_return(),
                'msg'  => $e->getMessage(),
            ]);
        }
        if ($e instanceof ValidateException) {
            return json([
                'code' => $e->getCode(),
                'data' => [],
                'msg'  => $e->getMessage(),
            ]);
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}
