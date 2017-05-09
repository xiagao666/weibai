/**
 * Created by ycp on 2017/4/13.
 */

var Product = {
    init: function() {
        var t = this;
        t.initUpload();
        t.bind();
    },
    // 事件统一绑定
    bind: function() {
        var t = this;
        $(document).on("change", "#JcategoryParent", function() {
            t.updateChildCategory();
        }).on("click", "#delProduct", function () {
            var $this = $(this);
            Modal.confirm({
                "id": "Jconfirm",
                "content": "确定要删除此产品？",
                "callback": function() {
                    t.deleteProduct($this.data("id"));
                }
            });
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
    },
    deleteProduct: function (productId) {
        var t = this;
        t.postData({
            url: "/product/delproduct",
            type: "post",
            data: {
                "productId": productId,
                "json": 1
            },
            callback: function(res) {
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": res.msg,
                        "type": "success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "type": "error",
                        "content": res.msg,
                        "callback": function() {}
                    });
                }
            }
        });
    },
    initUpload: function(){
        $('#JuploadDoc').fileupload({
            autoUpload: true,
            url: "/product/import?json=1",
            dataType: 'json',
            done: function (e, data) {
                if (data.result.status == "success"){
                    Modal.alert({
                        "id": "Jalert",
                        "content": "产品导入成功！",
                        "type": "success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "content": "产品导入失败！",
                        "type": "error",
                        "callback": function() {}
                    });
                }
            },
            fail: function (e, data) {
                Modal.alert({
                    "id": "Jalert",
                    "content": "产品导入失败！",
                    "type": "error",
                    "callback": function() {}
                });
            },
        });
    }
};

$(function() {
    Product.init();
});