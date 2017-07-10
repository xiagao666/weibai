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
				$this.addClass("active").find("ul").removeClass("v-visible");
			},
			"mouseleave": function(){
				var $this = $(this);
				if (window.location.href.indexOf("product") != -1){
					$this.find("ul").addClass("v-visible");
				} else {
					$this.removeClass("active").find("ul").addClass("v-visible");
				}
			}
		}, ".Jchild").on("click", "#Jgotop", function(){
			$(window).scrollTop(0);
		});
		$(window).scroll(function(){
			var $top = $("#Jgotop");
			if ($(window).scrollTop() > 0){
				$top.removeClass("v-hide");
			} else {
				$top.addClass("v-hide");
			}
		});
	},
	initSwiper: function(){
		var bannerSwiper = new Swiper('#JcommonSwiper', {
		    // 可选选项，自动滑动
		    autoplay: 5000,
		    pagination: '#JcommonSwiper .swiper-pagination',
		    // 循环
		    loop: true,
		    // 速度
		    speed: 300,
		    // 底部分页可点击
		    paginationClickable: true
		});

		$('.icon-left').on('click', function(e) {
            e.preventDefault()
            bannerSwiper.swipePrev()
        })
        $('.icon-right').on('click', function(e) {
            e.preventDefault()
            bannerSwiper.swipeNext()
        })
	}
};

$(function(){
	Base.init();
});