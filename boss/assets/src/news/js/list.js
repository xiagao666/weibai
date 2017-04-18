var News = {
    wangEditor: null,
    init: function() {
        this.bind();
        this.intWangEditor();
        $("#newsForm").hide();

    },
    intWangEditor: function() {
        var t = this;
        t.wangEditor = new wangEditor('editor-trigger');
        // 上传图片
        t.wangEditor.config.uploadImgUrl = '/upload/index?action=edimage';
        t.wangEditor.config.uploadImgFileName = 'upfile';
        t.wangEditor.create();
    },
    bind: function() {
        var t = this;
        $(document).on("click", "#editCms", function() {
            t.showEditForm($(this).data("id"));
        }).on("click", "#cancelEdit", function() {
            $("#newsForm").hide();
        }).on("click", "#saveEdit", function() {
            t.saveEditFormData();
        }).on("click", "#deleteCms", function() {
            var $this = $(this);
            Modal.confirm({
                "id": "Jmodal",
                "content": "确定要删除该条记录？",
                "callback": function() {
                    App.blockUI($("#Jmodal"));
                    t.deleteOne($this.data("id"));
                }
            });
        }).on("click", "#addNews", function() {
            t.showAddForm();
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
        var t = News;
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
        var t = News;
        if (rs == null) {
            return;
        }
        $("#cmsId").val(rs["id"]);
        $("#cmsTitle").val(rs["title"]);
        $("#cmsDes").val(rs["des"]);
        $("#cmsUrl").val(rs["hyperlink"]);
        t.wangEditor.$txt.html(rs["content"]);
        $("#newsForm").show();
    },
    saveEditFormData: function() {
        var t = this,
            id = $("#cmsId").val(),
            wEditorText = News.wangEditor.$txt.html(),
            urlData = '/cms/add?content=' + wEditorText;

        if (id > 0) {
            urlData = '/cms/update?content=' + wEditorText;
        }

        t.postData({
            url: urlData,
            data: $("#Jnewform").serialize(),
            type: "post",
            callback: function(res) {
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": "新闻添加成功！",
                        "type": "success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "type": "error",
                        "content": "新闻添加失败，请稍后重试！"
                    });
                }
            }
        });
    },
    deleteOne: function(id) {
        this.postData({
            url: '/cms/delete',
            data: {
                "cmsId": id,
                "json": 1
            },
            callback: function(res) {
                App.unblockUI($("#Jmodal"));
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": "删除成功！",
                        "type":"success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "type": "error",
                        "content": "删除失败，请稍后重试！"
                    });
                }
            }
        });
    },
    showAddForm: function() {
        $("#newsForm").show();
        $(':input', '#newsForm')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
        this.wangEditor.$txt.html('<p><br></p>');
    }
};

$(function() {
    News.init();
})