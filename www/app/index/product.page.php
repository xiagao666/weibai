<?php

/**
 * 产品相关
 */
class index_product extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 产品列表
     */
    public function pageIndex($inPath)
    {
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $key = isset($_GET['key']) ? core_lib_Comm::getStr($_GET["key"]) : '';
        $limit = 1;
        //查询产品信息
        $dbProduct = new core_db_Product();
        if ($key) {
            $query[] = "catalog_number like '%{$key}%' OR product like '%{$key}%' OR abbreviation like '%{$key}%' OR chinese_name like '%{$key}%' OR other_name like '%{$key}%'";
        }
        $products = $dbProduct->queryProductList($query, array("sort"=>"desc"), $limit, $page);
        core_lib_Comm::p($products);
        $this->pageBar($products['total'], $limit, $page, "/product/index");

        $dbViewHistory = new core_db_ViewHistory();
        $query['uuid'] = $this->_productViewLogQid;
        $views = $dbViewHistory->getViewHistorys($query);

        $this->_params["productList"] = $products['list'];
        $this->_params["viewList"] = $views['data'];
        return $this->render("product/list.html", $this->_params);
    }

    /**
     * 产品详情
     */
    public function pageDetail()
    {
        var_dump($this->_productViewLogQid);
        $id = isset($_GET['id']) ? core_lib_Comm::getStr($_GET["id"], 'int') : 0;
        if (!$id) {
            return $this->alert(array("status"=>"error", "msg"=>"缺少打开产品详情必要参数"));
        }

        $dbProduct = new core_db_Product();
        $product = $dbProduct->getProductById($id);
        core_lib_Comm::p($product);
        if ($product === false) {
            return $this->alert(array("status"=>"error", "msg"=>"打开的产品不存在"));
        }

        $dbProductDes = new core_db_ProductDes();
        $productDes = $dbProductDes->getProductDesByProductId($id);

        //添加浏览记录
        $viewData['uuid'] = $this->_productViewLogQid;
        $viewData['product_id'] = $id;
        $dbViewHistory = new core_db_ViewHistory();
        $dbViewHistory->addViewHistory($viewData);

        core_lib_Comm::p($productDes);
        $this->_params["product"] = $product;
        $this->_params["productDes"] = $productDes;
        $this->render("product/detail.html", $this->_params);
    }

    /**
     * 产品列表
     */
    public function pageList($inPath) {
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
