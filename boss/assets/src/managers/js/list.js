var Manager = {
    init: function() {
        var t = this;
        t.validate();
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
                    App.blockUI($("#Jconfirm"));
                    t.deleteUser($this.data("id"));
                }
            });
            // 重置密码
        }).on("click", ".Jreset", function() {
            var $this = $(this);
            Modal.confirm({
                "id": "Jconfirm",
                "content": "确定要重置该用户的密码？",
                "callback": function() {
                    App.blockUI($("#Jconfirm"));
                    t.resetPassword($this.data("id"));
                }
            });
            // 新增用户
        }).on("click", "#Jadd", function() {
            Modal.template({
                "id": "Jtemplate",
                "title": "添加用户",
                "content": t._template({
                    "iType": 0,
                    "managerId": "",
                    "sName": "",
                    "sTrueName": ""
                }),
                "callback": function() {
                    App.blockUI($("#Jtemplate"));
                    t.handleUser();
                }
            });
            // 编辑用户
        }).on("click", ".Jedit", function() {
            var $this = $(this);
            Modal.template({
                "id": "Jtemplate",
                "title": "修改用户信息",
                "content": t._template({
                    "iType": 1,
                    "managerId": $this.data("id"),
                    "sName": $this.data("name"),
                    "sTrueName": $this.data("truename")
                }),
                "callback": function() {
                    App.blockUI($("#Jtemplate"));
                    t.handleUser();
                }
            });
            // 解锁/锁定用户
        }).on("click", ".Jcontrol", function(){
            var $this = $(this),msg = "", lock = $this.data("lock");
            msg = lock ? "锁定": "解锁";
            Modal.confirm({
                "id": "Jconfirm",
                "content": "确定要"+msg+"该用户？",
                "callback": function() {
                    App.blockUI($("#Jconfirm"));
                    t.controlUser({
                        "managerId":$this.data("id"),
                        "isLock": lock,
                        "json": 1
                    });
                }
            });
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
        if ($("#Juserform").validate().form()){
            this.postData({
                url: "/manager/action",
                type: "post",
                data: $("#Juserform").serialize(),
                hObject: $("#Jtemplate"),
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
        }
    },
    // 解锁/锁定用户
    controlUser: function(config) {
        this.postData({
            url: "/manager/lock",
            type: "post",
            data: config,
            hObject: $("#Jconfirm"),
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
        this.postData({
            url: "/manager/delete",
            type: "post",
            data: {
                "managerId": iUid,
                "json": 1
            },
            hObject: $("#Jconfirm"),
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
        this.postData({
            url: "/manager/resetpassword",
            type: "post",
            data: {
                "managerId": iUid,
                "json": 1
            },
            hObject: $("#Jconfirm"),
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
        var content = '<form id="Juserform" action="#" class="form-horizontal">\
                        <input type="hidden" name="isEdit" value="' + config.iType + '" />\
                        <input type="hidden" name="json" value="1" />\
                        <input type="hidden" name="managerId" value="' + config.managerId + '" />\
                        <div class="alert alert-error hide">\
                            <button class="close" data-dismiss="alert"></button>\
                            <span>请输入您的用户名及密码</span>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label"><i class="icon-user"></i>用户名：</label>\
                            <div class="controls">\
                                <input id="JmanagerName" name="managerName" type="text" class="span6 m-wrap" palaceholder="请输入用户名" value="' + config.sName + '"/>\
                            </div>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label"><i class="icon-user-md"></i>真实姓名：</label>\
                            <div class="controls">\
                                <input id="JtrueName" name="trueName" type="text" class="span6 m-wrap" palaceholder="请输入真实姓名" value="' + config.sTrueName + '"/>\
                            </div>\
                        </div>\
                        <div class="control-group">\
                            <label class="control-label"><i class="icon-lock"></i>密码：</label>\
                            <div class="controls">\
                                <input id="Jpassword" name="password" type="password" class="span6 m-wrap" palaceholder="请输入密码" />\
                            </div>\
                        </div>';
        if (config.iType) {
            content += '</form>';
        } else {
            content += '<div class="control-group">\
                            <label class="control-label"><i class="icon-lock"></i>确认密码：</label>\
                            <div class="controls">\
                                <input id="JpasswordAgain" type="password" class="span6 m-wrap" palaceholder="请再次输入密码" />\
                            </div>\
                        </div>\
                    </form>';
        }

        return content;
    },
    // 数据校验
    validate: function(){
        $('#Juserform').validate({
            errorElement: 'label', //default input error message container
            errorClass: 'help-inline', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                }
            },

            messages: {
                username: {
                    required: "请输入用户名"
                },
                password: {
                    required: "请输入密码"
                }
            },

            invalidHandler: function(event, validator) { //display error alert on form submit   
                $('.alert-error', $('#Juserform')).show();
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
                error.addClass('help-small no-left-padding').insertAfter(element.closest('.input-icon'));
            },

            submitHandler: function(form) {
            }
        });
    }
};

$(function() {
    Manager.init();
});