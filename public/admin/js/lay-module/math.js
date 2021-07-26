layui.define(['laytpl', 'layer', 'jquery', 'setter'], function (exports) {
    var $ = layui.jquery
        , setter = layui.setter

    let math = {
        accAdd: function (arg1, arg2) {
            var r1, r2, max;
            try {
                r1 = arg1.toString().split(".")[1].length
            } catch (e) {
                r1 = 0
            }
            try {
                r2 = arg2.toString().split(".")[1].length
            } catch (e) {
                r2 = 0
            }
            max = Math.pow(10, Math.max(r1, r2))
            return (arg1 * max + arg2 * max) / max
        },
        // 减法
        accSub: function (arg1, arg2) {
            var r1, r2, max, min;
            try {
                r1 = arg1.toString().split(".")[1].length
            } catch (e) {
                r1 = 0
            }
            try {
                r2 = arg2.toString().split(".")[1].length
            } catch (e) {
                r2 = 0
            }
            max = Math.pow(10, Math.max(r1, r2));
            //动态控制精度长度
            min = (r1 >= r2) ? r1 : r2;
            return ((arg1 * max - arg2 * max) / max).toFixed(min)
        },
        // 乘法
        accMul: function (arg1, arg2) {
            var max = 0, s1 = arg1.toString(), s2 = arg2.toString();
            try {
                max += s1.split(".")[1].length
            } catch (e) {
            }
            try {
                max += s2.split(".")[1].length
            } catch (e) {
            }
            return Number(s1.replace(".", "")) * Number(s2.replace(".", "")) / Math.pow(10, max)
        },
        // 除法
        accDiv: function (arg1, arg2) {
            if (arg1 == null) {
                return 0;
            }
            var t1 = 0, t2 = 0, r1, r2;
            try {
                t1 = arg1.toString().split(".")[1].length
            } catch (e) {
            }
            try {
                t2 = arg2.toString().split(".")[1].length
            } catch (e) {
            }
            with (Math) {
                r1 = Number(arg1.toString().replace(".", ""))
                r2 = Number(arg2.toString().replace(".", ""))
                return (r1 / r2) * pow(10, t2 - t1)
            }
        },
        // 分转为元
        real_price: function(fen){
            return this.accDiv(fen,100);
        },
        fen: function(yuan){
            return this.accMul(yuan, 100);
        }
    };
    //对外接口
    exports('math', math);
});