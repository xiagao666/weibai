/**
 * Created by ycp on 2017/4/13.
 */

var News = {
    wangEditor:null,
    init:function(){
        App.init();

        this.bind();
        this.intWangEditor();
        $("#categoryForm").hide();

    },
    intWangEditor:function () {
        var t = this;
        t.wangEditor = new wangEditor('editor-trigger');
        // 上传图片
        t.wangEditor.config.uploadImgUrl = '/upload/index?action=edimage';
        t.wangEditor.config.uploadImgFileName = 'upfile';
        t.wangEditor.create();
    },
    bind:function(){
        var t = this;
        $(document).on("click","#editCms",function(){
            t.showEditForm($(this).data("id"));
        }).on("click","#cancelEdit",function(){
            $("#categoryForm").hide();
        }).on("click","#saveEdit",function(){
            t.saveEditFormData();
        }).on("click","#deleteCms",function () {
            t.deleteOne($(this).data("id"));
        }).on("click","#addNews",function () {
            t.showAddForm();
        });
    },
    postData:function(config){
        var t = this;
        try{
            $.ajax({
                url:config.url || "",
                data:config.data,
                dataType:"json",
                beforeSend:function(){
                },
                success:function(res){
                    if(config.callback != null){
                        config.callback(res);
                    }
                },
                error:function(res){

                },
                complete:function(res){

                }
            });
        }catch(e){
            console.error(e);
        }
    },
    showEditForm:function (id) {
        var param = {
          cmsId:id
        };
        var config = {url:'/cms/GetOneById',data:param,callback:this.setEditFormData};
        this.postData(config);
    },
    setEditFormData:function (rs) {
        var t = News;
        if(rs == null) {
            return;
        }
        $("#cmsId").val(rs["id"]);
        $("#cmsTitle").val(rs["title"]);
        $("#cmsDes").val(rs["des"]);
        $("#cmsUrl").val(rs["hyperlink"]);
        t.wangEditor.$txt.append('<p>'+rs["content"]+'</p>');
        $("#categoryForm").show();
    },
    saveEditFormData:function () {
        var id = $("#cmsId").val();
        var urlData = '/cms/add';
        var wEditorText = News.wangEditor.$txt.html();
        if(id > 0){
            urlData = '/cms/update?content=' + wEditorText;
        }
        console.log(wEditorText);
        var config = {url:urlData,data:$("#Jform").serializeArray(),callback:this.refreshTable};
        this.postData(config);
    },
    refreshTable:function (rs) {
        if(rs == null) {
            alert("操作失败");
        }
        //$("#delete_mod").modal('show');
        $("#categoryForm").hide();
        window.location.reload();
    },
    deleteOne:function (id) {
        var param = {
            cmsId:id
        };
        var config = {url:'/cms/delete',data:param};
        this.postData(config);
        window.location.reload();
    },
    showAddForm:function () {
        $("#categoryForm").show();
        $(':input','#categoryForm')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
        this.wangEditor.$txt.html('<p><br></p>');
    }
};

$(function(){
    News.init();
})
