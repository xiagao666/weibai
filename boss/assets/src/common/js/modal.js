var Modal = function() {
    var _template = "";

    function alert(config) {
        config = config || {};
        config.id = config.id || "Jalert";
        config.title = config.title || "系统提示";
        config.content = config.content || "系统提示消息";
        config.type = config.type || "info";
        config.callback = config.callback || {};
        var _alert = $("#" + config.id + ""),
            _icon = "",
            _btn = "";
        switch (config.type) {
            case "info":
                _icon = "icon-info-sign";
                _btn = "blue";
                break;
            case "warning":
                _icon = "icon-warning-sign";
                _btn = "yellow";
                break;
            case "error":
                _icon = "icon-remove-sign";
                _btn = "red";
                break;
            case "success":
                _icon = "icon-ok-sign";
                _btn = "green";
                break;
            default:
                break;
        }
        if (!_alert.length) {
            _template = '<div id="' + config.id + '" class="modal hide fade in" tabindex="-1" role="dialog" aria-hidden="false">\
				<div class="modal-header">\
					<h3>' + config.title + '</h3>\
				</div>\
				<div class="modal-body alert-' + config.type + '">\
					<i class="' + _icon + '"></i><span>' + config.content + '</span>\
				</div>\
				<div class="modal-footer">\
					<button id="JalertBtn" data-dismiss="modal" class="btn ' + _btn + '"><i class="icon-ok"></i>确 定</button>\
				</div>\
			</div>';
            $("body").append(_template);
            $("#" + config.id + "").modal("show");
        } else {
            if (config.title != "系统提示") {
                _alert.find("h3").html(config.title);
            }
            if (config.content != "系统提示消息") {
                _alert.find("span").html(config.content);
            }
            _alert.find(".modal-body").removeClass().addClass("modal-body alert-" + config.type);
            _alert.find(".modal-body i").removeClass().addClass(_icon);
            _alert.modal("show");
        }

        $("#JalertBtn").off("click");
        $("#JalertBtn").click(function() {
            config.callback();
        });
        $(".modal-backdrop").off("click");
    }

    function confirm(config) {
        config = config || {};
        config.id = config.id || "Jconfirm";
        config.title = config.title || "系统提示";
        config.content = config.content || "系统提示消息";
        config.callback = config.callback || {};
        var _confirm = $("#" + config.id + "");
        if (!_confirm.length) {
            _template = '<div id="' + config.id + '" class="modal hide fade in" tabindex="-1" role="dialog" aria-hidden="false">\
				<div class="modal-header">\
					<h3>' + config.title + '</h3>\
				</div>\
				<div class="modal-body alert">\
					<i class="icon-warning-sign"></i><span>' + config.content + '</span>\
				</div>\
				<div class="modal-footer">\
					<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i>取 消</button>\
					<button id="JconfirmBtn" data-dismiss="modal" class="btn yellow"><i class="icon-ok"></i>确 定</button>\
				</div>\
			</div>';
            $("body").append(_template);
            $("#" + config.id + "").modal("show");
        } else {
            if (config.title != "系统提示") {
                _confirm.find("h3").html(config.title);
            }
            if (config.content != "系统提示消息") {
                _confirm.find("span").html(config.content);
            }
            _confirm.modal("show");
        }

        $("#JconfirmBtn").off("click");
        $("#JconfirmBtn").click(function() {
            config.callback();
        });
        $(".modal-backdrop").off("click");
    }

    function template(config) {
        config = config || {};
        config.id = config.id || "Jtemplate";
        config.title = config.title || "系统提示";
        config.content = config.content || "系统提示消息";
        config.callback = config.callback || {};
        var _confirm = $("#" + config.id + "");
        if (!_confirm.length) {
            _template = '<div id="' + config.id + '" class="modal hide fade in" tabindex="-1" role="dialog" aria-hidden="false">\
				<div class="modal-header">\
					<h3>' + config.title + '</h3>\
				</div>\
				<div class="modal-body">\
					' + config.content + '\
				</div>\
				<div class="modal-footer">\
					<button class="btn" data-dismiss="modal" aria-hidden="true"><i class="icon-remove"></i>取 消</button>\
					<button id="JtemplateBtn" data-dismiss="modal" class="btn green"><i class="icon-ok"></i>确 定</button>\
				</div>\
			</div>';
            $("body").append(_template);
            $("#" + config.id + "").modal("show");
        } else {
            if (config.title != "系统提示") {
                _confirm.find("h3").html(config.title);
            }
            if (config.content != "系统提示消息") {
                _confirm.find(".modal-body").html(config.content);
            }
            _confirm.modal("show");
        }

        $("#JtemplateBtn").off("click");
        $("#JtemplateBtn").click(function() {
            config.callback();
        });
        $(".modal-backdrop").off("click");
    }
    return {
        alert: alert,
        confirm: confirm,
        template: template
    };
}();