var Index = {
	init: function(){
		var t = this;
		t.bind();
		t.initSwiper();
	},
	bind: function(){
		$(document).on("hover", ".Jchild", function(){
			var $this = $(this);
			$this.addClass("active").find("ul").removeClass("v-hide");
		}).on("mouseleave", ".Jchild", function(){
			var $this = $(this);
			$this.removeClass("active").find("ul").addClass("v-hide");
		});
	},
	initSwiper: function(){
		var mySwiper = new Swiper('.swiper-container', {
		    // 可选选项，自动滑动
		    autoplay: 5000,
		    pagination: '.swiper-pagination',
		    // 循环
		    loop: true,
		    // 速度
		    speed: 300,
		    // 底部分页可点击
		    paginationClickable: true
		});
	}
};

Index.init();