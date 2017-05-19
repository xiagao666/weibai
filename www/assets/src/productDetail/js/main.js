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
	}
};

$(function(){
	ProductDetail.init();
});