/**

 @Name： 图片选择器
 @Author：Siam
 @Site：

 */

layui.define(['form','laypage','upload', 'setter','view'], function(exports){
    let $ = layui.$
        ,layer = layui.layer
        ,setter = layui.setter
        ,view = layui.view
        ,form = layui.form;

    let laypage = layui.laypage;
    let upload = layui.upload;

    let imagemanage = {
        confirm_callable: null,// 用户自定义的确认回调
        layero: null,// layer的编号
        inited:false,

        config: {
            get_list_url: "",
            upload_url: "",
            delete_url: "",
            multiple: false,
        },
        init() {
            if (!!this.inited) return false;
            console.log("初始化");
            this.inited = true;
            let body = $("body");
            body.on("click", ".confirm_chooise", function(){
                imagemanage.onConfirm();
            });
            // 图片选中点击事件
            body.on("click", ".iamagemanage_one", function(){
                // 如果不能多选，则先取消所有其他已经选中的图片，再选中当前图片  multiple
                if (!imagemanage.config.multiple){
                    let chooise = $(".iamagemanage_chooise");
                    for (let i = 0; i < chooise.length; i++) {
                        chooise.eq(i).removeClass("iamagemanage_chooise");
                    }
                }
                if ($(this).hasClass("iamagemanage_chooise")){
                    $(this).removeClass("iamagemanage_chooise");
                }else{
                    $(this).addClass("iamagemanage_chooise");
                }
            });
            body.on("click", ".delete-image", function(){
                let id = $(this).data('image-id');
                imagemanage.delete(id);
            })
        },
        load(page = 1){
            view.req({
                url: this.config.get_list_url,
                data:{
                    page:page,
                    limit:16
                },
                type:"POST",
                success:function(res){
                    let html = '';

                    for (let i = 0; i < res.data.lists.length; i++) {
                        let temp = res.data.lists[i];
                        let url = res.data.base_url + temp.cover;
                        html += `
                    <div class="iamagemanage_one" data-image-id="${temp.id}" data-image-url="${url}" style="background-image: url('${url}')">
                        <div class="image_name">${temp.file_name}</div>
                        <div class="del" >
                            <span class="wi wi-delete2 delete-image" data-image-id="${temp.id}">删除</span>
                        </div>
                    </div>`;
                    }
                    $("#iamagemanage_list").html(html);
                    if (res.data.count > 0){
                        laypage.render({
                            elem: 'iamagemanage_page'
                            ,theme: '#1E9FFF'
                            ,count: res.data.count
                            ,limit:16
                            ,curr:page
                            ,jump: function(obj, first){
                                //首次不执行
                                if(!first){
                                    imagemanage.load(obj.curr);
                                }
                            }
                        });
                    }
                }
            });
        },
        upload(){
            view.req({
                url: imagemanage.config.delete_url,
                data:{
                    id:id
                },
                type:"POST",
                success:function(){
                    imagemanage.load();
                }
            })
        },
        delete(id){
            view.req({
                url: imagemanage.config.delete_url,
                data:{
                    id:id
                },
                type:"POST",
                success:function(){
                    imagemanage.load();
                }
            })
        },
        open:function(callable){
            this.init();
            this.confirm_callable = callable;
            let html = `
<style>
    .iamagemanage_one{
        height:200px;
        width: 200px;
        margin: 10px;
        text-align: center;
        vertical-align: middle;
        background-color: #eee;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: 50% 50%;
        position: relative;
        float:left;
    }
    .iamagemanage_one.iamagemanage_chooise{
        outline: 2px solid #428bca;
    }
    .iamagemanage_one:hover{
        outline: 2px solid #428bca;
    }
    .iamagemanage_one:hover .del{
        display: block;
    }
    .image_name{
        position: absolute;
        bottom: 0;
        left: 0;
        width: calc(100% - 40px);
        line-height: 34px;
        background: rgba(0,0,0,.5);
        color: #fff;
        padding: 0 20px;
        text-align: left;
        z-index: 2;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    .del {
        position: absolute;
        width: 34px;
        line-height: 34px;
        text-align: center;
        background-color: #428bca;
        cursor: pointer;
        bottom: 0;
        right: 0;
        z-index: 6;
        color:#fff;
        display: none;
    }
</style>
<div class='imagemanage' style="padding:10px;">
    <div class="layui-tab">
      <ul class="layui-tab-title">
        <li class="layui-this">图片列表</li>
        <li>上传图片</li>
      </ul>
      <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <div id="iamagemanage_list">
                
            </div>
            
            <div style="clear:both;"></div>
            <div id="iamagemanage_page"></div>
            <div class="layui-btn layui-btn-normal layui-btn-sm confirm_chooise" >确认选择</div>
        </div>
        <div class="layui-tab-item">
            <div class="layui-upload-drag" id="iamagemanage_upload">
                  <i class="layui-icon"></i>
                  <p>点击上传，或将文件拖拽到此处</p>
            </div>
        </div>
      </div>
    </div>
</div>
            `;
            layer.open({
                type: 1,
                content: html,
                title:"图片管理器",
                area:["70%","70%"],
                success: function(layero, index){
                    imagemanage.layero = index;
                    upload.render({
                        elem: '#iamagemanage_upload'
                        ,url: imagemanage.config.upload_url
                        ,data:{
                            access_token: layui.data(setter.tableName)[setter.request.tokenName]
                        }
                        , accept:"file"
                        ,done: function(res){
                            if (res.code !== '200'){
                                layer.alert(res.msg);
                                return false;
                            }
                            layer.msg('上传成功');
                            imagemanage.load();
                        }
                    });

                    imagemanage.load();
                }
            })
        },
        onConfirm(){
            let chooise = $(".iamagemanage_chooise");
            if (chooise.length === 0){
                layer.msg("请选择图片");return false;
            }
            let ids = [];
            for (let i = 0; i < chooise.length; i++) {
                ids.push({
                    id: chooise.eq(i).data('image-id'),
                    url: chooise.eq(i).data('image-url'),
                })
            }
            this.confirm_callable(ids);
        },
        close(){
            $(".confirm_chooise").unbind("click");
            $(".iamagemanage_one").unbind("click");
            layer.close(this.layero);
        }
    };


    //对外暴露的接口
    exports('imagemanage', imagemanage);
});