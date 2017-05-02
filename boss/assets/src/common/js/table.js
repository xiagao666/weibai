(function(){
    // table初始化
    TableList.init({
        // 操作对象
        "object": "#JdataPanel",
        // 数据表配置，参考jquery.datatable配置
        "tableConfig": {
            "bSort": false,
            "bPaginate": false,
            "searching": false,
            "bFilter":false,
            "bInfo":false
        },
        // 字段过滤
        "filter": ".Jfilter"
    });
    $(".dataTables_wrapper").find(".row-fluid").remove();
})();