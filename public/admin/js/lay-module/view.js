
layui.define(['laytpl', 'layer', 'jquery', 'setter'], function (exports) {
    var $ = layui.jquery
        , setter = layui.setter

    var view = {
        //清除 token，并跳转到登入页
        logout: function (callback) {
            //清空本地记录
            layui.data(setter.tableName, null);

            //跳转到登入页
            //location.hash = '/user/login';
            callback && callback();
        },

        //login成功后，写入缓存 ， 写入 access_token , 获取config
        login: function (token) {

            layui.data(setter.tableName, {
                key: setter.request.tokenName
                , value: token
            });
            view.get_config();

        },

        //获取用户配置信息缓存
        get_config : function () {
            view.req({
                url: "/admin/users/get_config",
                success: function (res) {
                    for (let key in res.data){
                        let temp = res.data[key];
                        layui.data(setter.tableName, {
                            key: key
                            , value: temp
                        });
                    }

                }
            });
        },
        //获取参数
        getQueryletiable: function(letiable) {
        let query = window.location.search.substring(1);
        let lets = query.split("&");
        for (let i = 0; i < lets.length; i++) {
            let pair = lets[i].split("=");
            if (pair[0] == letiable) {
                return pair[1];
            }
        }
        return (false);
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
            options.url = options.url.replace(/index.php/, '');
            options.url = options.url.replace(/\/\//g, '/');
            options.url = setter.Api + options.url;

            delete options.success;
            delete options.error;

            return $.ajax($.extend({
                type: 'get'
                , dataType: 'json'
                , success: function (res) {
                    var statusCode = response.statusCode;

                    //只有 response 的 code 一切正常才执行 done 和 success
                    if (res[response.statusName] == statusCode.ok) {
                        typeof options.done === 'function' && options.done(res);
                        typeof success === 'function' && success(res);
                    }

                    //登录状态失效，清除本地 access_token，并强制跳转到登入页
                    else if (res[response.statusName] == statusCode.logout) {
                        view.logout(function(){
                            window.location.href = './login.html';
                        });
                    }

                    //其它异常
                    else {
                        var error = [
                            '<cite>Error：</cite> ' + (res[response.msgName] || '返回状态码异常')
                            , debug()
                        ].join('');
                        view.error(error);
                        return false;
                    }
                }
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
            ,maxWidth: 500
            //,shade: 0.01
            ,offset: 't'
            // ,anim: 6
            ,id: 'SiamAdminError'
        }, options))
    };
    //对外接口
    exports('view', view);
});