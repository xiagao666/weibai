/**
 * Created by ycp on 2017/4/13.
 */

var About = {
    wangEditor: null,
    init: function() {
        App.init();

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
            $("#aboutForm").hide();
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
        var  t = this,
            wEditorText = t.wangEditor.$txt.html(),
            urlData = '/cms/update?content=' + wEditorText;
        t.postData({
            url: urlData,
            data: $("#Jform").serialize(),
            callback: t.refreshTable
        });
    },
    refreshTable: function(rs) {
        if (rs == null) {
            alert("操作失败");
        } else {
            alert("操作成功");
        }
        window.location.reload();
    }
};

$(function() {
    About.init();
})