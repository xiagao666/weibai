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
		});
	}
};

$(function(){
	ProductDetail.init();
});