/**
 * Created by ycp on 2017/4/13.
 */

var Product = {
    data: {},
    init: function() {
        App.init();

        // table初始化
        TableList.init({
            // 操作对象
            "object": "#JdataPanel",
            // 数据表配置，参考jquery.datatable配置
            "tableConfig": {
                "aoColumnDefs": [{
                    "aTargets": [0]
                }],
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
                    "sEmptyTable": "无记录",
                    "sInfo": "当前第<span style='color:#35aa47'>_START_</span>页 / 共计<span style='color:#35aa47'>_END_</span>页 / 共计<span style='color:#35aa47'>_TOTAL_</span>条记录",
                    "sInfoEmpty": "当前第<span style='color:#35aa47'>0</span>页 / 共计<span style='color:#35aa47'>0</span>页 / 共计<span style='color:#35aa47'>0</span>条记录",
                    "loadingRecords": "加载中...",
                    "processing": "处理中...",
                    "sSearch": "搜索:",
                    "sZeroRecords": "未查询到记录",
                },
                "iDisplayLength": 10,
            },
            // 字段过滤
            "filter": ".Jfilter"
        });

        this.bind();
    },
    // 
    bind: function() {
        var t = this;
        $(document).on("click", "#Jpost", function() {
            t.search();
        }).on("change", "#categoryParent", function() {
            t.updateChildCategory();
        });
    },
    postData: function(config) {
        var t = this;
        try {
            $.ajax({
                url: config.url || "",
                data: config.data,
                type: config.type || "get",
                dataType: "json",
                beforeSend: function() {},
                success: function(res) {
                    config.callback(res);
                },
                error: function(res) {

                },
                complete: function(res) {

                }
            });
        } catch (e) {
            console.error(e);
        }
    },
    search: function() {
        window.location.href = ""
    },
    updateChildOption: function(res) {
        if (res == null) {
            return;
        }
        var data = res['categorys'];
        if (data) {
            for (var i = 0; i < data.length; i++) {
                var categoryChild = data[i];
                var o = new Option(categoryChild["name"], categoryChild["id"]);
                $("#categoryChild").append(o);
            }
        }
    },
    updateChildCategory: function() {
        var t = this;
        //更新二级类目
        $("#categoryChild").empty();
        var parentCategoryId = $("#categoryParent").val();
        if (parentCategoryId == 0) {
            return;
        }
        var o = new Option("请选择", 0);
        $("#categoryChild").append(o);
        var param = {
            categoryId: parentCategoryId
        };
        var config = {
            url: '/category/GetChildsByParentId',
            data: param,
            callback: t.updateChildOption
        };
        this.postData(config);
    },
    // 序列化参数
    serialize:function(obj){
        return "?"+obj.serialize();
    }
};

$(function() {
    Product.init();
})