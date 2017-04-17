var Login = {
    init: function() {
        App.init();
        $('#Jform').validate({
            errorElement: 'label', //default input error message container
            errorClass: 'help-inline', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true
                },
                remember: {
                    required: false
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
                $('.alert-error', $('.login-form')).show();
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
                window.location.href = "index.html";
            }
        });

        $('#Jform input').keypress(function(e) {
            if (e.which == 13) {
                $("#Jsubmit").trigger();
            }
        });


        $(document).on("click", "#Jsubmit", function() {
        	if ($('#Jform').validate().form()) {
                $.ajax({
	                url: "/sign/in",
	                type: "post",
	                data: $("#Jform").serialize(),
	                success: function(res) {
	                    try {
	                    	res = JSON.parse(res);
	                        if (res.status == "success") {
	                            window.location.href = res.backUrl;
	                        } else {
	                        	$(".alert-error").find("span").html(res.msg);
	                        	$('.alert-error').show();
	                        }
	                    } catch (e) {
	                        console.error(e);
	                    }
	                },
	                complete: function() {

	                }
	            });
            }
        });
    }
};

$(function() {
    Login.init();
})