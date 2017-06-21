var New = {
	init: function(){
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