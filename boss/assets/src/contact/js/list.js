var About = {
    wangEditor: null,
    init: function() {
        this.bind();
        this.intWangEditor();
    },
    intWangEditor: function() {
        var t = this;
        t.wangEditor = new wangEditor('editor-trigger-about');
        // 上传图片
        t.wangEditor.config.uploadImgUrl = '/upload/index?action=edimage';
        t.wangEditor.config.uploadImgFileName = 'upfile';
        t.wangEditor.create();
    },
    bind: function() {
        var t = this;
        $(document).on("click", "#cancelEdit", function() {
            t.resetForm();
        }).on("click", "#saveEdit", function() {
            t.saveEditFormData();
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
    saveEditFormData: function() {
        var t = this,
            id = $("#cmsId").val(),
            wEditorText = t.wangEditor.$txt.html(),
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
                        "content": "操作成功！",
                        "type": "success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "type": "error",
                        "content": "操作失败，请稍后重试！",
                        "callback": function(){}
                    });
                }
            }
        });
    },
    refreshTable: function(rs) {
        if (rs == null) {
            alert("操作失败");
        } else {
            alert("操作成功");
        }
        window.location.reload();
    },
    resetForm:function () {
        var t = this;
        t.wangEditor.$txt.html('<p><br></p>');
    }
};

$(function() {
    About.init();
});