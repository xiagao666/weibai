/**
 * Created by ycp on 2017/4/13.
 */

var News = {
    data:{},
    init:function(){
        App.init();

        this.bind();

        $("#categoryForm").hide();

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
        if(rs == null) {
            return;
        }
        $("#cmsId").val(rs["id"]);
        $("#cmsTitle").val(rs["title"]);
        $("#cmsDes").val(rs["des"]);
        $("#cmsUrl").val(rs["hyperlink"]);
        $("#categoryForm").show();
    },
    saveEditFormData:function () {
        var id = $("#cmsId").val();
        var urlData = '/cms/add';
        if(id > 0){
            urlData = '/cms/update';
        }
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
    }
};

$(function(){
    News.init();
})
