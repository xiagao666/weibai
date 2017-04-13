/**
 * Created by ycp on 2017/4/13.
 */
var selectChild = function (parent) {
    $("#category_child").empty();
    var parent_category_id = $("#category_parent").val();
    if(parent_category_id == "" || parent_category_id == 0){
        return false;
    }
    $.getJSON("/product/getList","{'categoryIds':"+parent_category_id+"}",
        function (data) {
        alert(data);
        var o = new Option("请选择", 0);
        $("#category_child").append(o);
        if(data == ""){
            return;
        }
        for(var i=0; i<data.length;i++) {
            var categoryChild = data[i];
            var o = new Option(categoryChild["name"],categoryChild["id"]);
            $("#category_child").append(o);
        }
    }
    );
}
