/**
 * Created by ycp on 2017/4/13.
 */

var News = {
    init: function() {
        this.bind();
    },
    // 事件统一绑定
    bind: function() {
        var t = this;
        $(document).on("click", "#cmsSubmit", function () {
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
        $cmsForm = $("#cmsForm");
        t.postData({
            url: "/cms/action",
            type: "post",
            data: $cmsForm.serialize(),
            "callback": function(res){
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": res.msg,
                        "type": "success",
                        "callback": function() {
                            window.location.href = "/cms/other";
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