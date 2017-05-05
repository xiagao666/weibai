var Base = {
	init: function(){
		var t = this;
		t.bind();
		t.initSwiper();
	},
	bind: function(){
		$(document).on({
			"mouseenter": function(){
				var $this = $(this);
				$this.addClass("active").find("ul").removeClass("v-hide");
			},
			"mouseleave": function(){
				var $this = $(this);
				if (window.location.href.indexOf("product") != -1){
					$this.find("ul").addClass("v-hide");
				} else {
					$this.removeClass("active").find("ul").addClass("v-hide");
				}
			}
		}, ".Jchild");
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

$(function(){
	Base.init();
});