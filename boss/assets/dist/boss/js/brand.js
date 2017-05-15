/**
 * Created by ycp on 2017/4/13.
 */

var Brand = {
    wangEditor: null,
    init: function() {
        App.init();

        this.bind();
        $("#brandForm").hide();

    },
    bind: function() {
        var t = this;
        $(document).on("click", "#editCms", function() {
            t.showEditForm($(this).data("id"));
        }).on("click", "#cancelEdit", function() {
            $("#brandForm").hide();
        }).on("click", "#saveEdit", function() {
            t.saveEditFormData();
        }).on("click", "#deleteCms", function() {
            $("#Jdelete").data("id",$(this).data("id"));
        }).on("click", "#addNews", function() {
            t.showAddForm();
        }).on("click","#Jdelete",function(){
            t.deleteOne($(this).data("id"));
        });
    },
    postData: function(config) {
        var t = this;
        try {
            $.ajax({
                url: config.url || "",
                data: config.data,
                type: config.type || "get",
                dataType: "json",
                beforeSend: function() {},
                success: function(res) {
                    if (config.callback != null) {
                        config.callback(res);
                    }
                },
                error: function(res) {

                },
                complete: function(res) {

                }
            });
        } catch (e) {
            console.error(e);
        }
    },
    showEditForm: function(id) {
        var t = this
        var param = {
            cmsId: id
        };
        var config = {
            url: '/cms/GetOneById',
            data: param,
            callback: t.setEditFormData
        };
        t.postData(config);
    },
    setEditFormData: function(rs) {
        var t = Brand;
        if (rs == null) {
            return;
        }
        $("#cmsId").val(rs["id"]);
        $("#cmsTitle").val(rs["title"]);
        $("#cmsDes").val(rs["des"]);
        $("#cmsUrl").val(rs["hyperlink"]);
        $("#brandForm").show();
    },
    saveEditFormData: function() {
        var  t = this,
            id = $("#cmsId").val(),
            urlData = '/cms/add';

        if (id > 0) {
            urlData = '/cms/update';
        }

        t.postData({
            url: urlData,
            data: $("#Jform").serialize(),
            callback: t.refreshTable
        });
    },
    refreshTable: function(rs) {
        if (rs == null) {
            alert("操作失败");
        }else{
            alert("操作成功");
        }
        window.location.reload();
    },
    deleteOne: function(id) {
        var param = {
            cmsId: id
        };
        var config = {
            url: '/cms/delete',
            data: param
        };
        this.postData(config);
        window.location.reload();
    },
    showAddForm: function() {
        $("#brandForm").show();
        $(':input', '#brandForm')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
    }
};

$(function() {
    Brand.init();
})