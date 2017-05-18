var ProductDetail = {
	init: function(){
		var t = this;
		t.bind();
	},
	bind: function(){
		$(document).on("click", ".Jtab", function(){
			var $this = $(this), $content = $("#Jcontent").find(".cotent-main");
			$this.siblings().removeClass("active");
			$content.addClass("v-hide");
			$this.addClass("active");
			$content.eq($this.index()).removeClass("v-hide");
		}).on("click", "#JclearHistroy", function(){
			$.ajax({
                url: "/product/delproductviewhistory?json=1",
                type: "post",
                dataType: "json",
                success: function(res){
                    if (res.status == "success"){
                        var $history = $("#Jhistory");
                        $("<p class='info-none'>暂时没有任何浏览记录</p>").appendTo($history);
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
	}
};

$(function(){
	ProductDetail.init();
});