var Product = {
	init: function(){
		var t = this;
        t.initPage();
		t.bind();
	},
	bind: function(){
		$(document).on("hover", ".Jnav", function(){
			var $this = $(this);
			$this.siblings().removeClass("active");
			$this.addClass("active");
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