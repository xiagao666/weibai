/**
 * Created by ycp on 2017/4/13.
 */

var Detail = {
    $productForm: null,//产品表单
    init: function() {
        this.bind();
        this.initWangEditor();
    },
    initWangEditor: function() {
        var t = this;
        t.wangEditor = new wangEditor('JeditorDes');
        // 上传图片
        t.wangEditor.config.uploadImgUrl = '/upload/index?action=edimage';
        t.wangEditor.config.uploadImgFileName = 'upfile';
        t.wangEditor.create();
    },
    // 事件统一绑定
    bind: function() {
        var t = this;
        $(document).on("change", "#JcategoryParent", function() {
            t.updateChildCategory();
        }).on("click", "#productSumbit", function () {
            t.postProductData();
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
    postProductData: function () {
        var  t = this;
        $productForm = $("#productForm");
        // if (t.productValidate()) {
            t.postData({
                url: "/product/action",
                type: "post",
                data: $productForm.serialize(),
                "callback": function(res){
                    if (res.status == "success") {
                        Modal.alert({
                            "id": "Jalert",
                            "content": res.msg,
                            "type": "success",
                            "callback": function() {
                                window.location.href = "/product/list";
                            }
                        });
                    } else {
                        Modal.alert({
                            "id": "Jalert",
                            "content": res.msg,
                            "type": "error",
                            "callback": function() {
                            }
                        });
                    }
                }
            });
        // }
    },
    productValidate: function () {
        var t = this;
        t.$productForm.validate({
            rules: {
                catalogNumber: "required",
                package: "required"
            },
            messages: {
                managerName: {
                    required: "请输入货号"
                },
                trueName: {
                    required: "请输入包装"
                }
            }
        });
    }
};

$(function() {
    Detail.init();
});