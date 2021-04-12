layui.define(['laytpl', 'layer','jquery'], function (exports) {
    var $ = layui.jquery


        //对外接口ss
        , view = function (id) {
            return new Class(id);
        }

        , SHOW = 'layui-show', LAY_BODY = 'LAY_app_body'

        //构造器
        , Class = function (id) {
            this.id = id;
            this.container = $('#' + (id || LAY_BODY));
        };

    //自定义请求字段
    var request = {
        tokenName: 'access_token' //自动携带 token 的字段名（如：access_token）。可设置 false 不携带。
    }

    //自定义响应字段
    var response = {
        statusName: 'code' //数据状态的字段名称
        , statusCode: {
            ok: 200 //数据状态一切正常的状态码
            , logout: 1001 //登录状态失效的状态码
        }
        , msgName: 'msg' //状态信息的字段名称
        , dataName: 'data' //数据详情的字段名称
    }
    var tableName = 'Siam_Admin' //本地存储表名

    //Ajax请求
    view.req = function (options) {
        var that = this
            , success = options.success
            , error = options.error

        options.data = options.data || {};
        options.headers = options.headers || {};

        if (request.tokenName) {
            var sendData = typeof options.data === 'string'
                ? JSON.parse(options.data)
                : options.data;

            //自动给参数传入默认 token
            options.data[request.tokenName] = request.tokenName in sendData
                ? options.data[request.tokenName]
                : (layui.data(tableName)[request.tokenName] || '');

            //自动给 Request Headers 传入 token
            options.headers[request.tokenName] = request.tokenName in options.headers
                ? options.headers[request.tokenName]
                : (layui.data(tableName)[request.tokenName] || '');
        }

        delete options.success;
        delete options.error;

        return $.ajax($.extend({
            type: 'get'
            , dataType: 'json'
            , success: function (res) {
                var statusCode = response.statusCode;

                //只有 response 的 code 一切正常才执行 done
                if (res[response.statusName] == statusCode.ok) {
                    typeof options.done === 'function' && options.done(res);
                }

                //登录状态失效，清除本地 access_token，并强制跳转到登入页
                else if (res[response.statusName] == statusCode.logout) {
                    view.exit();
                }

                //其它异常
                else {
                    var error = [
                        '<cite>Error：</cite> ' + (res[response.msgName] || '返回状态码异常')
                    ].join('');
                    view.error(error);
                }

                //只要 http 状态码正常，无论 response 的 code 是否正常都执行 success
                typeof success === 'function' && success(res);
            }
            , error: function (e, code) {
                var error = [
                    '请求异常，请重试<br><cite>错误信息：</cite>' + code
                ].join('');
                view.error(error);

                typeof error === 'function' && error(res);
            }
        }, options));
    };


    //对外接口
    exports('view', view);
});