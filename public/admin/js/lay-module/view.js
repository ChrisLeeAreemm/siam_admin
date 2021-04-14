
layui.define(['laytpl', 'layer', 'jquery', 'setter'], function (exports) {
    var $ = layui.jquery
        , setter = layui.setter

    var view = {
        //清除 token，并跳转到登入页
        logout: function (callback) {
            //清空本地记录的 token
            layui.data(setter.tableName, {
                key: setter.request.tokenName
                , remove: true
            });

            //跳转到登入页
            //location.hash = '/user/login';
            callback && callback();
        },
        //login成功后，写入 access_token
        login: function (token) {
            layui.data(setter.tableName, {
                key: setter.request.tokenName
                , value: token
            });
        },

        //Ajax请求
        req: function (options) {
            var that = this
                , success = options.success
                , error = options.error
                , request = setter.request
                , response = setter.response
                , debug = function () {
                return setter.debug
                    ? '<br><cite>URL：</cite>' + options.url
                    : '';
            };

            options.data = options.data || {};
            options.headers = options.headers || {};

            if (request.tokenName) {
                var sendData = typeof options.data === 'string'
                    ? JSON.parse(options.data)
                    : options.data;

                //自动给参数传入默认 token
                options.data[request.tokenName] = request.tokenName in sendData
                    ? options.data[request.tokenName]
                    : (layui.data(setter.tableName)[request.tokenName] || '');

                //自动给 Request Headers 传入 token
                options.headers[request.tokenName] = request.tokenName in options.headers
                    ? options.headers[request.tokenName]
                    : (layui.data(setter.tableName)[request.tokenName] || '');
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
                            , debug()
                        ].join('');
                        view.error(error);
                    }

                    //只要 http 状态码正常，无论 response 的 code 是否正常都执行 success
                    typeof success === 'function' && success(res);
                }
                //TODO  需要做一个报错兼容 Chris  view.error
                , error: function (e, code) {
                    var error = [
                        '请求异常，请重试<br><cite>错误信息：</cite>' + code
                        , debug()
                    ].join('');
                    view.error(error);

                    typeof error === 'function' && error(res);
                }
            }, options));
        },

    };
    //弹窗
    view.popup = function(options){
        var success = options.success
            ,skin = options.skin;

        delete options.success;
        delete options.skin;

        return layer.open($.extend({
            type: 1
            ,title: '提示'
            ,content: ''
            ,id: 'LAY-system-view-popup'
            ,skin: 'layui-layer-admin' + (skin ? ' ' + skin : '')
            ,shadeClose: true
            ,closeBtn: false
            ,success: function(layero, index){
                var elemClose = $('<i class="layui-icon" close>&#x1006;</i>');
                layero.append(elemClose);
                elemClose.on('click', function(){
                    layer.close(index);
                });
                typeof success === 'function' && success.apply(this, arguments);
            }
        }, options))
    };
    //异常提示
    view.error = function(content, options){
        return view.popup($.extend({
            content: content
            ,maxWidth: 300
            //,shade: 0.01
            ,offset: 't'
            ,anim: 6
            ,id: 'LAY_adminError'
        }, options))
    };
    //对外接口
    exports('view', view);
});