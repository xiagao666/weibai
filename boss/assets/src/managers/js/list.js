var Manager = {
    $userForm: null,
    $template: null,
    init: function() {
        var t = this;
        t.bind();
    },
    // 事件统一绑定
    bind: function() {
        var t = this;
        // 删除用户
        $(document).on("click", ".Jdelete", function() {
            var $this = $(this);
            Modal.confirm({
                "id": "Jconfirm",
                "content": "确定要删除该用户？",
                "callback": function() {
                    t.deleteUser($this.data("id"));
                }
            });
            t.$template = $("#Jconfirm");
            // 重置密码
        }).on("click", ".Jreset", function() {
            var $this = $(this);
            Modal.confirm({
                "id": "Jconfirm",
                "content": "确定要重置该用户的密码？",
                "callback": function() {
                    t.resetPassword($this.data("id"));
                }
            });
            t.$template = $("#Jconfirm");
            // 新增/编辑用户
        }).on("click", ".Joperate", function() {
            var $this = $(this),
                msg = "",
                type = $this.data("type");
            msg = type ? "修改用户信息" : "添加用户";
            Modal.template({
                "id": "Jtemplate",
                "title": msg,
                "content": t._template({
                    "iType": type,
                    "managerId": $this.data("id"),
                    "sName": $this.data("name"),
                    "sTrueName": $this.data("truename")
                }),
                "callback": function() {
                    t.handleUser();
                }
            });
            t.$template = $("#Jtemplate");
            t.$userForm = $("#JuserForm");
            t.$userForm.find(".alert-error button").click(function() {
                $(this).parent().hide();
            });
            $("#JtemplateBtn").removeData("dismiss").removeAttr("data-dismiss");
            t.validate();
            // 解锁/锁定用户
        }).on("click", ".Jcontrol", function() {
            var $this = $(this),
                msg = "",
                lock = $this.data("lock");
            msg = lock ? "锁定" : "解锁";
            Modal.confirm({
                "id": "Jconfirm",
                "content": "确定要" + msg + "该用户？",
                "callback": function() {
                    t.controlUser({
                        "managerId": $this.data("id"),
                        "isLock": lock,
                        "json": 1
                    });
                }
            });
            t.$template = $("#Jconfirm");
        });
    },
    // 数据发送
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
                    config.callback(res);
                },
                error: function(res) {
                    console.error("异常....");
                },
                complete: function() {
                    App.unblockUI(config.hObject);
                }
            });
        } catch (e) {
            console.error(e);
        }
    },
    // 添加/编辑用户
    handleUser: function() {
        var t = this;
        if (t.$userForm.validate().form()) {
            t.postData({
                url: "/manager/action",
                type: "post",
                data: t.$userForm.serialize(),
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
                        t.$userForm.find(".alert-error").show().find("span").html(res.msg);
                    }
                }
            });
        }
    },
    // 解锁/锁定用户
    controlUser: function(config) {
        var t = this;
        t.postData({
            url: "/manager/lock",
            type: "post",
            data: config,
            hObject: t.$template.find(".Jload"),
            callback: function(res) {
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
                        "type": "error",
                        "content": res.msg,
                        "callback": function() {}
                    });
                }
            }
        });
    },
    // 删除用户
    deleteUser: function(iUid) {
        var t = this;
        t.postData({
            url: "/manager/delete",
            type: "post",
            data: {
                "managerId": iUid,
                "json": 1
            },
            hObject: t.$template.find(".Jload"),
            callback: function(res) {
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
                        "type": "error",
                        "content": res.msg,
                        "callback": function() {}
                    });
                }
            }
        });
    },
    // 重置用户密码
    resetPassword: function(iUid) {
        var t = this;
        t.postData({
            url: "/manager/resetpassword",
            type: "post",
            data: {
                "managerId": iUid,
                "json": 1
            },
            hObject: t.$template.find(".Jload"),
            callback: function(res) {
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": res.msg,
                        "type": "success",
                        "callback": function() {}
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
    },
    // 模板渲染
    _template: function(config) {
        var content = '<form id="JuserForm" action="#" class="form-horizontal">\
                        <input type="hidden" name="isEdit" value="' + config.iType + '" />\
                        <input type="hidden" name="json" value="1" />\
                        <input type="hidden" name="managerId" value="' + config.managerId + '" />\
                        <div class="alert alert-error hide">\
                            <button class="close" type="button"></button>\
                            <span>请完善相关信息</span>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label"><i class="icon-user"></i>用户名：</label>\
                            <div class="controls">\
                                <input id="managerName" name="managerName" type="text" class="span6 m-wrap" palaceholder="请输入用户名" value="' + config.sName + '"/>\
                            </div>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label"><i class="icon-user-md"></i>真实姓名：</label>\
                            <div class="controls">\
                                <input id="trueName" name="trueName" type="text" class="span6 m-wrap" palaceholder="请输入真实姓名" value="' + config.sTrueName + '"/>\
                            </div>\
                        </div>';
        if (config.iType) {
            content += '</form>';
        } else {
            content += '<div class="control-group">\
                            <label class="control-label"><i class="icon-lock"></i>密码：</label>\
                            <div class="controls">\
                                <input id="password" name="password" type="password" class="span6 m-wrap" palaceholder="请输入密码" />\
                            </div>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label"><i class="icon-lock"></i>确认密码：</label>\
                            <div class="controls">\
                                <input name="confirm_password" type="password" class="span6 m-wrap" palaceholder="请再次输入密码" />\
                            </div>\
                        </div>\
                    </form>';
        }

        return content;
    },
    // 数据校验
    validate: function() {
        var t = this;
        t.$userForm.validate({
            errorElement: 'label', //default input error message container
            errorClass: 'help-inline', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                managerName: {
                    required: true
                },
                trueName: {
                    required: true
                },
                password: {
                    required: true,
                    rangelength: [6, 10]
                },
                confirm_password: {
                    required: true,
                    equalTo: "#password"
                }
            },
            messages: {
                managerName: {
                    required: "请输入用户名"
                },
                trueName: {
                    required: "请输入您的真实姓名"
                },
                password: {
                    required: "请输入密码",
                    rangelength: $.validator.format("密码由{0}至{1}位字符组成")
                },
                confirm_password: {
                    required: "请再次输入密码",
                    equalTo: "两次密码不一致"
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-error', t.$userForm).show();
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
    Manager.init();
});