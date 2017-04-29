var Brand = {
    wangEditor: null,
    init: function() {
        this.bind();
    },
    bind: function() {
        var t = this;
        $(document).on("click", ".Jedit", function() {
            t.showEditForm($(this).data("id"));
        }).on("click", ".Jdelete", function() {
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
                        "content": res.msg,
                        "callback": function(){}
                    });
                }
            }
        });
    }
};

$(function() {
    Brand.init();
});