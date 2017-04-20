/**
* Function: 可视化数据表，依赖于jquery.datatable及select2
* Author:   huangchaowei
* Email:    
* Date:     2017-04-11
* Version:  V1.0
* Modify：
**/
var TableList = function(){
	// 初始化datatable
	var initTable = function(config){
		var $object = $(config.object),
			oTable = $object.find("table").dataTable(config.tableConfig || {});

		// 统一修改样式
		$object.find("input[type='text'],select").addClass("m-wrap small");
		// 初始化select2
		$object.find("select").select2();
		// 表头字段筛选
		$object.find(config.filter || "").find("input[type='checkbox']").change(function(){
			var iCol = parseInt($(this).attr("data-column")),
            	bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
            oTable.fnSetColumnVis(iCol, (bVis ? false : true));
		});
	};

	// 返回实例化方法
	return {
		init: function(config){
			if (!jQuery().dataTable) {
                return;
            }

            if (!config.object){
            	try{
            		console.error("请传入需要操作的对象!");
            	} catch (e){
            		alert(e);
            	}

            	return;
            }
            initTable(config);
		}
	};
}();