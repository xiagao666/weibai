var Category = {
    wangEditor: null,
    init: function() {
        this.bind();
        //this.intWangEditor();
        $("#categoryForm").hide();

    },
    intWangEditor: function() {
        var t = this;
        t.wangEditor = new wangEditor('editor-trigger');
        // 上传图片
        t.wangEditor.config.uploadImgUrl = '/upload/index?action=edimage';
        t.wangEditor.config.uploadImgFileName = 'upfile';
        t.wangEditor.create();
    },
    bind: function() {
        var t = this;
        $(document).on("click", ".Jedit", function() {
            t.showEditForm($(this).data("id"));
        }).on("click", "#cancelEdit", function() {
            $("#categoryForm").hide();
        }).on("click", "#saveEdit", function() {
            t.saveEditFormData();
        }).on("click", ".Jdelete", function() {
            var $this = $(this);
            Modal.confirm({
                "id": "Jmodal",
                "content": "确定要删除该条记录？",
                "callback": function() {
                    App.blockUI($("#Jmodal"));
                    t.deleteOne($this.data("id"));
                }
            });
        }).on("click", "#addNews", function() {
            t.showAddForm();
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
                    if (config.callback != null) {
                        config.callback(res);
                    }
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
    showEditForm: function(id) {
        var t = Category;

        var param = {
            categoryId: id,
            json:1
        };
        var config = {
            url: '/category/getOneCategoryById',
            data: param,
            callback: t.setEditFormData
        };
        t.postData(config);
    },
    setEditFormData: function(rs) {
        var t = Category;
        if (rs.status == "error") {
            Modal.alert({
                "id": "Jalert",
                "content": "内部错误！",
                "type": "error",
                "callback": function() {
                    window.location.reload();
                }
            });
        }else{
            try {
                var data = rs.data['category'],
                    pCategorys = rs.data['pCategorys'];
                if (data) {
                    if(data['pid'] == 0){
                        $("#JfpCategoryControl").hide();
                    }else{
                        $("#JFormCategoryParent").empty();
                        var sHtml = "<option value='0'>请选择一级类目</option>";
                        for (var i = 0; i < pCategorys.length; i++) {
                            var categoryItem = pCategorys[i];
                            if(data['pid'] == categoryItem['id']){
                                sHtml += "<option selected='selected' value=" + categoryItem["id"] + ">" + categoryItem["name"] + "</option>";
                            }else{
                                sHtml += "<option value=" + categoryItem["id"] + ">" + categoryItem["name"] + "</option>";
                            }

                        }
                        $("#JFormCategoryParent").append(sHtml);
                    }
                    $("#name").val(data["name"]);
                    $("#des").val(data["des"]);
                    $("#showSort").val(data["show_sort"]);
                    $("#categoryId").val(data["id"]);
                    $("#cmsUrl").val(data["hyperlink"]);
                    //t.wangEditor.$txt.html(rs["content"]);
                    $("#categoryForm").show();
                }
            } catch (e) {
                console.error(e);
            }

        }
    },
    saveEditFormData: function() {
        var t = this,
            categoryId = $("#categoryId").val(),
            isEdit = 0;
        if(categoryId > 0){
            isEdit = 1;
        }
        t.postData({
            url: "/category/actionCategory?isEdit="+isEdit,
            data: $("#Jnewform").serialize(),
            type: "post",
            callback: function(res) {
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": "操作成功！",
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
                        "callback": function(){}
                    });
                }
            }
        });
    },
    deleteOne: function(id) {
        this.postData({
            url: '/category/delete',
            data: {
                "categoryId": id,
                "json": 1
            },
            type: "post",
            callback: function(res) {
                App.unblockUI($("#Jmodal"));
                if (res.status == "success") {
                    Modal.alert({
                        "id": "Jalert",
                        "content": "删除成功！",
                        "type":"success",
                        "callback": function() {
                            window.location.reload();
                        }
                    });
                } else {
                    Modal.alert({
                        "id": "Jalert",
                        "type": "error",
                        "content": "删除失败，请稍后重试！",
                        "callback": function(){}
                    });
                }
            }
        });
    },
    showAddForm: function() {
        $("#categoryForm").show();
        $(':input', '#categoryForm')
            .not(':button, :submit, :reset, :hidden')
            .val('')
            .removeAttr('checked')
            .removeAttr('selected');
        //this.wangEditor.$txt.html('<p><br></p>');
    }
};

$(function() {
    Category.init();
});