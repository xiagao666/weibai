var Tech = {
	init: function(){
		var t = this;
		t.initPage();
		t.bind();
	},
	bind: function(){
	},
	initPage: function(){
		var currPage = $("input[name='currPage']").val();
        var totalPage = $("input[name='totalPage']").val();
        $(".tcdPageCode").createPage({
            pageCount: parseInt(totalPage),
            current: parseInt(currPage),
            backFn:function(p){
                window.location.href = "/main/tech?page="+p;
            }
        });
	}
};

$(function(){
	Tech.init();
});