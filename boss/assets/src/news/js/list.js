var News = {
    $newForm: null,
    wangEditor: null,
    $template: null,
    init: function() {
        this.bind();
    },
    intWangEditor: function() {
        var t = this;
        t.wangEditor = new wangEditor('Jeditor');
        t.wangEditor.config.menus = [
            'source',
            '|',
            'bold',
            'underline',
            'italic',
            'strikethrough',
            'eraser',
            'forecolor',
            'bgcolor',
            '|',
            'quote',
            'fontfamily',
            'fontsize',
            'head',
            'unorderlist',
            'orderlist',
            'alignleft',
            'aligncenter',
            'alignright',
            '|',
            'link',
            'unlink',
            'table',
            'emotion',
            '|',
            'img',
            'video',
            'location',
            'insertcode',
            '|',
            'undo',
            'redo',
            'fullscreen',
            'lineheight',
            'indent'
        ];
        // 上传图片
        t.wangEditor.config.uploadImgUrl = '/upload/index?action=edimage';
        t.wangEditor.config.uploadImgFileName = 'upfile';
        t.wangEditor.create();
    },
    bind: function() {
        var t = this;
        // 删除记录
        $(document).on("click", ".Jdelete", function() {
            var $this = $(this);
            Modal.confirm({
                "id": "Jconfirm",
                "content": "确定要删除该条新闻？",
                "callback": function() {
                    t.deleteNew($this.data("id"));
                }
            });
            t.$template = $("#Jconfirm");
            // 新增/编辑新闻
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
                beforeSend: function() {
                    App.blockUI(config.hObject);
                },
                success: function(res) {
                    if (config.callback != null) {
                        config.callback(res);
                    }
                },
                error: function(res) {

                },
                complete: function() {
                    App.unblockUI(config.hObject);
                }
            });
        } catch (e) {
            console.error(e);
        }
    },
    // 删除新闻
    deleteNew: function(id) {
        var t = this;
        t.postData({
            url: '/cms/delete',
            data: {
                "cmsId": id,
                "json": 1
            },
            hObject: t.$template.find(".Jload"),
            callback: function(res) {
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": "删除成功！",
                        "type": "success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "type": "error",
                        "content": res.msg,
                        "callback": function() {}
                    });
                }
            }
        });
    }
};

$(function() {
    News.init();
});