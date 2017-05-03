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
	}
};
$(function(){
    New.init();
});