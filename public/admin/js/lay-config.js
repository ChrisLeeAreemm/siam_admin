/**
 * date:2019/08/16
 * author:Mr.Chung
 * description:此处放layui自定义扩展
 * version:2.0.4
 */

window.rootPath = (function (src) {
    src = document.scripts[document.scripts.length - 1].src;
    return src.substring(0, src.lastIndexOf("/") + 1);
})();

layui.config({
    base: rootPath + "lay-module/",
    version: true
}).extend({
    miniAdmin: "layui-mini/miniAdmin", // layuimini后台扩展
    miniMenu: "layui-mini/miniMenu", // layuimini菜单扩展
    miniTab: "layui-mini/miniTab", // layuimini tab扩展
    miniTheme: "layui-mini/miniTheme", // layuimini 主题扩展
    miniTongji: "layui-mini/miniTongji", // layuimini 统计扩展
    step: 'step-lay/step', // 分步表单扩展
    treetable: 'treetable-lay/treetable', //table树形扩展
    tableSelect: 'table-select/tableSelect', // table选择扩展
    iconPickerFa: 'icon-picker/iconPickerFa', // fa图标选择扩展
    echarts: 'echarts/echarts', // echarts图表扩展
    echartsTheme: 'echarts/echartsTheme', // echarts图表主题扩展
    wangEditor: 'wang-editor/wangEditor', // wangEditor富文本扩展
    layarea: 'lay-area/layarea', //  省市县区三级联动下拉选择器
    dtree: 'dtree/dtree' //  tree
});
