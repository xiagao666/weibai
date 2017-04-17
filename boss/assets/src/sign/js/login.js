
$(function () {
    App.init();
    $('.login-form').validate({
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
                required: "请输入正确用户名"
            },
            password: {
                required: "请输入正确密码"
            }
        },

        invalidHandler: function (event, validator) { //display error alert on form submit
            $('.alert-error', $('.login-form')).show();
        },

        highlight: function (element) { // hightlight error inputs
            $(element)
                .closest('.control-group').addClass('error'); // set error class to the control group
        },

        success: function (label) {
            label.closest('.control-group').removeClass('error');
            label.remove();
        },

        errorPlacement: function (error, element) {
            error.addClass('help-small no-left-padding').insertAfter(element.closest('.input-icon'));
        },

        submitHandler: function (form) {
            window.location.href = "/sign/in";
        }
    });

    $('.login-form input').keypress(function (e) {
        if (e.which == 13) {
            if ($('.login-form').validate().form()) {
                $.ajax({
                    url: "/sign/in",
                    type: "POST",
                    dataType: "json",
                    beforeSend: function() {},
                    success: function(res) {
                        window.location.href = "/product/list";
                        // config.callback(res);
                    },
                    error: function(res) {
                        $('.alert-error', $('.login-form')).show();
                    },
                    complete: function(res) {

                    }
                });
            }
            return false;
        }
    });
})