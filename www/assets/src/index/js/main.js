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

		new Swiper('#JnewsSwiper', {
		    // 可选选项，自动滑动
		    autoplay: 5000,
		    pagination: '#JnewsSwiper .swiper-pagination',
		    // 循环
		    loop: true,
		    // 速度
		    speed: 300,
		    // 底部分页可点击
		    paginationClickable: true,
		    onSlideChangeEnd: function(swiper){
		    	var index = swiper.activeLoopIndex,
		    	$nav = $("#JnewsNav"),
		    	margin = parseInt($("#JnewsList").find("li:eq(0)").css("margin-bottom"));
		    	$nav.css("top", ($nav.height()+margin) * index);
		    }
		});
	}
};

$(function(){
	Index.init();
});