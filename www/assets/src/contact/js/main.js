var Contact = {
	$name: $("#Jname"),
	$phone: $("#Jphone"),
	$suggest: $("#Jsuggest"),
	init: function(){
		var t = this;
		t.bind();
	},
	bind: function(){
		var t = this;
		$(document).on("click", "#Jsend", function(){
			if(t.check()){
                t.sendMail();
            }
		}).on("focus", "input,textarea", function(){
			$(this).parent().removeClass("error");
		}).on("click", "#Jreset", function(){
			var $form = $("#Jform");
			$form.find(".error").removeClass("error");
		});
	},
	check: function(){
		var t = this;
		if (t.$name.val() == "") {
			var $parent = t.$name.parent();
			$parent.addClass("error").find(".tip").text("请输入您的姓名！");
			return false;
		}

		if (t.$phone.val() == "") {
			var $parent = t.$phone.parent();
			$parent.addClass("error").find(".tip").text("请输入您的电话号码！");
			return false;
		}
		if (!/^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0-9]|17[0-9])\d{8}$/.test(t.$phone.val())) {
			var $parent = t.$phone.parent();
			$parent.addClass("error").find(".tip").text("请输入正确的电话号码！");
			return false;
		}

		if (t.$suggest.val() == "") {
			var $parent = t.$suggest.parent();
			$parent.addClass("error").find(".tip").text("请输入您的意见！");
			return false;
		}

		return true;
	},
	sendMail: function(){
		var t = this,
        $mail = $("#JsendMail"),
        body = "userName：" + t.$name.val() + "  userPhone："+t.$phone.val() + "  userSuggest：" + t.$suggest.val(),
        mail = "mailto:info@viobio.com.cn?subject=suggest&body="+body;
        window.location.href = mail;
	},
	reset: function(){
		$("#Jcontact").find("input,textarea").val("");
	}
};

$(function(){
	Contact.init();
})
