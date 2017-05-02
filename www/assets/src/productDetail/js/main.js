var Main = {
	init: function(){
		var t = this;
		t.bind();
	},
	bind: function(){
		$(document).on("click", ".Jtab", function(){
			var $this = $(this);
			$this.siblings().removeClass("active").find("div").addClass("v-hide");
			$this.addClass("active").find("div").removeClass("v-hide");
		});
	}
};

$(function(){
	Main.init();
});