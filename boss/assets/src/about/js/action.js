/**
 * Created by ycp on 2017/4/13.
 */

var News = {
    init: function() {
        this.bind();
        this.initWangEditor();
    },
    initWangEditor: function() {
        var t = this;
        t.wangEditor = new wangEditor('JeditorDes');
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
    // 事件统一绑定
    bind: function() {
        var t = this;
        $(document).on("click", "#aboutSubmit", function () {
            t.postNewsData();
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
                    config.callback(res);
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
    postNewsData: function () {
        var  t = this;
        $aboutForm = $("#aboutForm");
        t.postData({
            url: "/cms/action",
            type: "post",
            data: $aboutForm.serialize(),
            "callback": function(res){
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": res.msg,
                        "type": "success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "content": res.msg,
                        "type": "error",
                        "callback": function() {
                        }
                    });
                }
            }
        });
    }
};

$(function() {
    News.init();
});