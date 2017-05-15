var Index = {
	init: function(){
		var t = this;
		t.initSwiper();
	},
	bind: function(){
	},
	initSwiper: function(){
		new Swiper('#JproductSwiper', {
		    // 可选选项，自动滑动
		    autoplay: 5000,
		    pagination: '#JproductSwiper .swiper-pagination',
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
	Index.init();
});