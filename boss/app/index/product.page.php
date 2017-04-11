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
        /*$dbCategory = new core_db_Category();
        $condition["pid"] = 0;
        $rs = $dbCategory->queryAllCategory($condition);
        $condition["pid"] != 0;
        $rsItems = $dbCategory->queryAllCategory($condition);
        foreach ($rs as $k => $item) {
            foreach ($rsItems as $key => $rv) {
                if ( $item['id'] == $rv['pid'] ) {
                    $rs[$k]['son'][$key] = $rv;
                }
            }
        }*/
        $dbProduct = new core_db_Product();
        $productRs = $dbProduct->queryProductList("",1,20,"");
        // $param["categorys"] = $rs;
        $param["products"] = $productRs->items;
        return $this->render("boss/productList.html", $param);
    }
    //产品列表
    public function pageGetList($inPath) {
        $categoryIdList = $_GET["categoryIds"];//,各个子类目ID用，号隔开
        $categoryIds = explode(",", $categoryIdList);
        $categoryIds = array_filter($categoryIds);//去掉空元素
        $category =  implode(",", $categoryIds);
        $page = $_GET["page"];
        $size = $_GET["size"];
        $query = array("category_id in ({$category})");
        $dbProduct = new core_db_Product();
        $productRs = $dbProduct->queryProductList($query,$page,$size,"id desc");
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
