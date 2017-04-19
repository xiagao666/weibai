/**
 * Created by ycp on 2017/4/13.
 */

var Product = {
    init: function() {
        this.bind();
    },
    // 事件统一绑定
    bind: function() {
        var t = this;
        $(document).on("change", "#JcategoryParent", function() {
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
    updateChildOption: function(res) {
        if (res == null) {
            console.log("未查询到对应的二级类目");
            return;
        }
        try {
            var data = res['categorys'];
            if (data) {
                var sHtml = "";
                for (var i = 0; i < data.length; i++) {
                    var categoryChild = data[i];
                    sHtml += "<option value=" + categoryChild["id"] + ">" + categoryChild["name"] + "</option>";
                }
                $("#JcategoryChild").append(sHtml);
            }
        } catch (e) {
            console.error(e);
        }
    },
    updateChildCategory: function() {
        var t = this,
            parentCategoryId = $("#JcategoryParent").val();
        // 更新二级类目
        $("#JcategoryChild option:gt(0)").remove();
        $("#JcategoryChild option:first").trigger("change");

        if (parentCategoryId == 0) {
            return;
        }

        t.postData({
            url: '/category/GetChildsByParentId',
            data: {
                "categoryId": parentCategoryId
            },
            callback: t.updateChildOption
        });
    }
};

$(function() {
    Product.init();
});