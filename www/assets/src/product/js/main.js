var Product = {
    // cache the selected nav
    _$nav: $(".Jnav.active"),
	init: function(){
		var t = this;
        t.initPage();
		t.bind();
	},
	bind: function(){
        var t = this;
		$(document).on({
            "mouseenter": function(){
                var $this = $(this);
                $this.addClass("active").siblings().removeClass("active");
            },
            "mouseleave": function(){
                var $this = $(this);
                $this.removeClass("active");
                t._$nav.addClass("active");
            }
        }, ".Jnav").on("click", "#JclearHistroy", function(){
            swal({
              title: "您确定要清空浏览记录?",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#dd6b55",
              confirmButtonText: "确定",
              cancelButtonText: "取消",
              closeOnConfirm: false,
              closeOnCancel: true,
              showLoaderOnConfirm: true
            },
            function(){
                $.ajax({
                    url: "/product/delproductviewhistory?json=1",
                    type: "post",
                    dataType: "json",
                    success: function(res){
                        if (res.status == "success"){
                            var $history = $("#Jhistory");
                            $("<p class='info-none'>暂时没有任何浏览记录</p>").appendTo($history);
                            $history.find("ul,button").remove();
                            swal({
                              title: "浏览记录清空成功!",
                              timer: 500,
                              showConfirmButton: false
                            });
                        } else {
                            swal("系统提示", "浏览记录删除失败 :)", "error");
                        }
                    },
                    error: function(){
                        swal("系统提示", "浏览记录删除失败 :)", "error");
                    }
                });
            });
        });
	},
    initPage: function(){
        var currPage = $("input[name='currPage']").val();
        var totalPage = $("input[name='totalPage']").val();
        var url = $("input[name='url']").val();
        $(".tcdPageCode").createPage({
            pageCount: parseInt(totalPage),
            current: parseInt(currPage),
            backFn:function(p){
                window.location.href = url+p;
            }
        });
    }
};

$(function(){
	Product.init();
});
