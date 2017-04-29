var Main = {
	init: function(){
		var t = this;
		t.bind();
	},
	bind: function(){
		$(document).on("hover", ".Jchild", function(){
			var $this = $(this);
			$this.addClass("active").find("ul").removeClass("v-hide");
		}).on("mouseleave", ".Jchild", function(){
			var $this = $(this);
			$this.removeClass("active").find("ul").addClass("v-hide");
		});
	}
};

Main.init();