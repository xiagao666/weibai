<?php

class index_product extends index_base
{
    function __construct()
    {
    }

    function __destruct()
    {
    }

    /**
     * 首页
     */
    public function pageList($inPath)
    {
        //查询类目信息
        $dbCategory = new core_db_Category();
        $condition["pid"] = 0;
        $rs = $dbCategory->queryAllCategory($condition);
        $param["pCategory"] = $rs['items'];

        $dbProduct = new core_db_Product();
        $productRs = $dbProduct->queryProductList("",1,20,"");
        $param["products"] = $productRs->items;
        $param["columns"] = core_lib_Comm::getTableColumns(PRODUCT_COLUMNS);
        return $this->render("boss/productList.html", $param);
    }
    //产品列表
    public function pageGetList($inPath) {
        $categoryParentId = $_GET["categoryParentId"];//父ID
        $categoryChildId = $_GET["categoryChildId"];//子ID
        if($categoryChildId == 0 && $categoryParentId > 0) {
            $dbCategory = new core_db_Category();
            $condition['pid'] = $categoryParentId;
            $rs = $dbCategory->queryAllCategory($condition);
            if($rs['items']){
                foreach ($rs['items'] as $v) {
                    $ids[] = $v['id'];
                }
            }
        }
        if($categoryChildId > 0){
            $ids[] = $categoryChildId;
        }
        $category = explode(",", $ids);
        $page = $_GET["page"];
        $limit = $_GET["limit"];
        if(!empty($ids)) {
            $query = array("category_id in ({$category})");
        }
        $dbProduct = new core_db_Product();
        $productRs = $dbProduct->queryProductList($query,$page,$limit,"id desc");
        $param['items'] = $productRs->items;
        $page['total'] = $productRs->totalSize;
        $param['limit'] = $limit;
        $param['page'] = $page;
        $param['categoryParentId'] = $categoryParentId;
        $param['categoryChildId'] = $categoryChildId;
        echo json_encode($productRs);
    }

    //产品明细信息
    public function pageDetail($inPath) {
        $productId = $_GET["id"];
        $dbProduct = new core_db_Product();
        $info = $dbProduct->getOneProductById($productId);//产品详细信息
        $dbProductDes = new core_db_ProductDes();
        $des = $dbProductDes->getOneProductDesByProductId($productId);//产品描述文档
        $dbProductRel = new core_db_ProductRelation();
        $rel = $dbProductRel->getOneProductRelByProductId($productId);//产品关联内容
        $param["info"] = $info;
        $param["des"] = $des;
        $param["rel"] = $rel;
        echo json_encode($param);
    }

}
