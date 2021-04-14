/**

 */

layui.define(['layer'], function (exports) {
    exports('setter', {
        name: 'Siam_Admin'
        , tableName: 'Siam_Admin' //本地存储表名
        //自定义请求字段
        , request: {
            tokenName: 'access_token' //自动携带 token 的字段名（如：access_token）。可设置 false 不携带。
        }
        ,debug: true //是否开启调试模式。如开启，接口异常时会抛出异常 URL 等信息

        //自定义响应字段
        , response: {
            statusName: 'code' //数据状态的字段名称
            , statusCode: {
                ok: 200 //数据状态一切正常的状态码
                , logout: 1001 //登录状态失效的状态码
            }
            , msgName: 'msg' //状态信息的字段名称
            , dataName: 'data' //数据详情的字段名称
        }
    });
});
