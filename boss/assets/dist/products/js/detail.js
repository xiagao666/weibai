var Detail={$productForm:null,init:function(){this.bind(),this.initWangEditor()},initWangEditor:function(){var t=this;t.wangEditor=new wangEditor("JeditorDes"),t.wangEditor.config.uploadImgUrl="/upload/index?action=edimage",t.wangEditor.config.uploadImgFileName="upfile",t.wangEditor.create()},bind:function(){var t=this;$(document).on("change","#JcategoryParent",function(){t.updateChildCategory()}).on("click","#productSumbit",function(){t.postProductData()})},postData:function(t){try{$.ajax({url:t.url||"",data:t.data,type:t.type||"get",dataType:"json",beforeSend:function(){},success:function(a){t.callback(a)},error:function(t){},complete:function(t){}})}catch(t){console.error(t)}},updateChildOption:function(t){if(null==t)return void console.log("未查询到对应的二级类目");try{var a=t.categorys;if(a){for(var o="",e=0;e<a.length;e++){var r=a[e];o+="<option value="+r.id+">"+r.name+"</option>"}$("#JcategoryChild").append(o)}}catch(t){console.error(t)}},updateChildCategory:function(){var t=this,a=$("#JcategoryParent").val();$("#JcategoryChild option:gt(0)").remove(),$("#JcategoryChild option:first").trigger("change"),0!=a&&t.postData({url:"/category/GetChildsByParentId",data:{categoryId:a},callback:t.updateChildOption})},postProductData:function(){var t=this;$productForm=$("#productForm"),t.postData({url:"/product/action",type:"post",data:$productForm.serialize(),callback:function(t){"success"==t.status?Modal.alert({id:"Jalert",content:t.msg,type:"success",callback:function(){window.location.href="/product/list"}}):Modal.alert({id:"Jalert",content:t.msg,type:"error",callback:function(){}})}})},productValidate:function(){this.$productForm.validate({rules:{catalogNumber:"required",package:"required"},messages:{managerName:{required:"请输入货号"},trueName:{required:"请输入包装"}}})}};$(function(){Detail.init()});