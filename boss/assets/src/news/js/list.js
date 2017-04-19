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
        }).on("click", ".Joperate", function() {
            var $this = $(this),
                msg = "",
                id = $this.data("id");
            msg = id ? "修改新闻信息" : "添加新闻";
            Modal.template({
                "id": "Jtemplate",
                "title": msg,
                "class": "v-news",
                "content": t._template({
                    "id": id
                }),
                "callback": function() {
                    t.handleNew({
                        "id": id
                    });
                }
            });
            t.$template = $("#Jtemplate");
            t.$newForm = $("#JnewForm");
            t.$newForm.find(".alert-error button").click(function() {
                $(this).parent().hide();
            });
            $("#JtemplateBtn").removeData("dismiss").removeAttr("data-dismiss");
            t.validate();
            t.intWangEditor();
            if (id){
                t.postData({
                    "url": "/cms/getonebyid",
                    "type": "get",
                    "data": {"cmsId": id, "json":1},
                    "callback": function(res){
                        if (res.status == "success") {
                            var data = res.data;
                            $("#cmsId").val(data["id"]);
                            $("#title").val(data["title"]);
                            $("#cmsDes").val(data["des"]);
                            $("#cmsUrl").val(data["hyperlink"]);
                            t.wangEditor.$txt.html(data["content"]);
                        } else {
                            t.$newForm.find(".alert-error").show().find("span").html(res.msg);
                        }
                    }
                });
            }
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
                        "content": "删除失败，请稍后重试！",
                        "callback": function() {}
                    });
                }
            }
        });
    },
    // 新增/编辑新闻
    handleNew: function(config) {
        var t = this;
        if (t.$newForm.validate().form()) {
            var url = "",text = t.wangEditor.$txt.html();
            if (config.id){
                url = '/cms/update?content=' + text;
            } else {
                url = '/cms/add?content=' + text;
            }
            t.postData({
                url: url,
                type: "post",
                data: t.$newForm.serialize(),
                hObject: t.$template.find(".Jload"),
                callback: function(res) {
                    if (res.status == "success") {
                        t.$template.modal("hide");
                        Modal.alert({
                            "id": "Jalert",
                            "content": res.msg,
                            "type": "success",
                            "callback": function() {
                                window.location.reload();
                            }
                        });
                    } else {
                        t.$newForm.find(".alert-error").show().find("span").html(res.msg);
                    }
                }
            });
        }
    },
    // 模板渲染
    _template: function(config) {
        var content = '<form id="JnewForm" action="#" class="form-horizontal">\
                        <input type="hidden" name="cmsType" value="1" />\
                        <input type="hidden" name="json" value="1" />\
                        <input id="cmsId" name="cmsId" type="hidden" value="'+config.id+'"/>\
                        <div class="alert alert-error hide">\
                            <button class="close" type="button"></button>\
                            <span>请完善相关信息</span>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label">标题：</label>\
                            <div class="controls">\
                                <input id="title" name="title" type="text" class="span6 m-wrap" />\
                            </div>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label">简述：</label>\
                            <div class="controls">\
                                <textarea id="cmsDes" name="des" class="span6 m-wrap" rows="3"></textarea>\
                            </div>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label">链接地址：</label>\
                            <div class="controls">\
                                <input id="cmsUrl" name="hyperlink" type="text" class="span6 m-wrap" />\
                            </div>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label">内容：</label>\
                            <div class="controls">\
                                <div id="Jeditor"><p><br/></p></div>\
                            </div>\
                        </div>\
                    </form>';

        return content;
    },
    // 数据校验
    validate: function() {
        var t = this;
        t.$newForm.validate({
            errorElement: 'label', //default input error message container
            errorClass: 'help-inline', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                title: {
                    required: true
                }
            },
            messages: {
                title: {
                    required: "请输入新闻标题"
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-error', t.$newForm).show();
            },

            highlight: function(element) { // hightlight error inputs
                $(element)
                    .closest('.control-group').addClass('error'); // set error class to the control group
            },

            success: function(label) {
                label.closest('.control-group').removeClass('error');
                label.remove();
            },

            errorPlacement: function(error, element) {
                error.addClass('help-small no-left-padding').insertAfter(element.closest('.span6.m-wrap'));
            },

            submitHandler: function(form) {
                return false;
            }
        });
    }
};

$(function() {
    News.init();
});