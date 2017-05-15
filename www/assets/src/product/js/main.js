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
            $.ajax({
                url: "/product/delproductviewhistory?json=1",
                type: "post",
                dataType: "json",
                success: function(res){
                    if (res.status == "success"){
                        var $history = $("#Jhistory");
                        $("<p>暂时没有任何浏览记录</p>").appendTo($history);
                        $history.find("ul,button").remove()
                    } else {
                        alert("清空失败，请稍后重试!");
                    }
                },
                error: function(){
                    alert("清空失败，请稍后重试!");
                }
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