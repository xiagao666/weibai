<?php

/**
 * 产品相关
 */
class index_product extends STpl
{
    function __construct()
    {
    }

    function __destruct()
    {
    }

    /**
     * 产品列表
     */
    public function pageIndex($inPath)
    {
        //查询类目信息
        $dbCategory = new core_db_Category();
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
        }
        //查询产品信息
        $dbProduct = new core_db_Product();
        $productRs = $dbProduct->queryProductList("",1,20,"");
        $param["categorys"] = $rs;
        $param["products"] = $productRs;
        return $this->render("index/product.html", $param);
    }

    /**
     * 产品详情
     */
    public function pageDetail($inPath)
    {
        $productId = $_GET["productId"];
        $dbProduct = new core_db_Product();
        $condition["id"] = $productId;
        $baseInfo = $dbProduct->getOne($condition);
        $dbProductDes = new core_db_ProductDes();
        $productDes = $dbProductDes->getOneProductDesByProductId($productId);
        $param["baseInfo"] = $baseInfo;
        $param["$productDes"] = $productDes;
        $this->render("index/pdetail.html");
    }

    /**
     * 文献、文章type=1/产品说明书type=2
     */
    public function pageProductRelation($inPath) {
        $productId = $_GET["productId"];
        $type = $_GET["type"];
        $dbProductRelation = new core_db_ProductRelation();
        $condition["product_id"] = $productId;
        $condition["type"] = $type;
        $rs = $dbProductRelation->queryProductRelationList($condition);
        echo json_encode($rs);
    }
}
