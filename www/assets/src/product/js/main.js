var Main = {
	init: function(){
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
		var t = this;
		t.bind();
	},
	bind: function(){
		$(document).on("click", ".Jnav", function(){
			var $this = $(this);
			$this.siblings().removeClass("active");
			$this.addClass("active");
		}).on("hover", ".Jchild", function(){
            var $this = $(this);
            $this.addClass("active").find("ul").removeClass("v-hide");
        }).on("mouseleave", ".Jchild", function(){
            var $this = $(this);
            $this.removeClass("active").find("ul").addClass("v-hide");
        });
	}
};

$(function(){
	Main.init();
});