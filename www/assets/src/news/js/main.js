var New = {
	init: function(){
        var currPage = $("input[name='currPage']").val();
        var totalPage = $("input[name='totalPage']").val();
        $(".tcdPageCode").createPage({
            pageCount: parseInt(totalPage),
            current: parseInt(currPage),
            backFn:function(p){
                window.location.href = "/news/index?page="+p;
            }
        });
		var t = this;
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
        }, ".Jnav");
    }
};
$(function(){
    New.init();
});