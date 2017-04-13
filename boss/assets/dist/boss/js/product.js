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


var Product = {
    data:{},
    init:function(){
        App.init();

        // table初始化
        TableList.init({
            // 操作对象
            "object":"#JdataPanel",
            // 数据表配置，参考jquery.datatable配置
            "tableConfig":{
            "aoColumnDefs": [
                { "aTargets": [ 0 ] }
            ],
            "aaSorting": [],
            "aLengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"]
            ],
            "oLanguage": {
                "sLengthMenu": "_MENU_ 条记录每页",
                "oPaginate": {
                    "sPrevious": "上一页",
                    "sNext": "下一页"
                },
                "sEmptyTable":     "无记录",
                "sInfo":      "当前第<span style='color:#35aa47'>_START_</span>页 / 共计<span style='color:#35aa47'>_END_</span>页 / 共计<span style='color:#35aa47'>_TOTAL_</span>条记录",
                "sInfoEmpty":      "当前第<span style='color:#35aa47'>0</span>页 / 共计<span style='color:#35aa47'>0</span>页 / 共计<span style='color:#35aa47'>0</span>条记录",
                "loadingRecords": "加载中...",
                "processing":     "处理中...",
                "sSearch":         "搜索:",
                "sZeroRecords":    "未查询到记录",
            },
            "iDisplayLength": 10,
        },
        // 字段过滤
        "filter":".Jfilter"
        });

        this.bind();
    },
    // 
    bind:function(){
        var t = this;
        $(document).on("click","#Jpost",function(){
            t.search();
        }).on("change","#Jtype", function(){
            var val =$(this).val();
        });
    },
    postData:function(config){
        var t = this;
        try{
            $.ajax({
                url:config.url || "",
                data:config.data,
                dataType:"json",
                beforeSend:function(){
                },
                success:function(res){
                    config.calllback(res);
                },
                error:function(res){

                },
                complete:function(res){

                }
            });
        }catch(e){
            console.error(e);
        }
    },
    search:function(){
        window.location.href = ""
    },
    afs:function(){
    }
};

$(function(){
    Product.init();
})