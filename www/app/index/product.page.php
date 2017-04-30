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
        $parentCategoryId = isset($_GET['pid']) ? core_lib_Comm::getStr($_GET["pid"]) : 0;//父级ID
        $chidlCategoryId = isset($_GET['cid']) ? core_lib_Comm::getStr($_GET["cid"]) : 0;//子级ID
        $limit = 3;
        //查询产品信息
        $dbProduct = new core_db_Product();
        if ($parentCategoryId && !$chidlCategoryId) {
            $dbCategory = new core_db_Category();
            $categoryIds = $dbCategory->queryAllCategory(array('pid'=>$parentCategoryId), 1000, 1);
            $childCategoryIds = array_column($categoryIds['items'], 'id');
            if (is_array($childCategoryIds)) {
                $query[] = "category_id in ".implode(",", $childCategoryIds);
            }
        }
        if ($chidlCategoryId) {
            $query['category_id'] = $chidlCategoryId;
        }
        if ($key) {
            $query[] = "catalog_number like '%{$key}%' OR product like '%{$key}%' OR abbreviation like '%{$key}%' OR chinese_name like '%{$key}%' OR other_name like '%{$key}%'";
        }

        $products = $dbProduct->queryProductList($query, array("sort"=>"desc"), $limit, $page);
        core_lib_Comm::p($products);
        $this->pageBar($products['total'], $limit, $page, "/product/index");

        $dbViewHistory = new core_db_ViewHistory();
        $query['uuid'] = $this->_productViewLogQid;
        $views = $dbViewHistory->getViewHistorys($query);
        $viewsProductIds = is_array($views['data']) ? array_column($views['data'], 'product_id') : '';
        if (is_array($viewsProductIds)) {
            $viewsProductIds = array_unique($viewsProductIds);
            $viewProductQuery[] = "id in (".implode(",", $viewsProductIds).")";
            $viewProducts = $dbProduct->queryProductList($viewProductQuery, "", count($viewsProductIds), 1);
            if ($viewsProductIds) {
                foreach ($viewsProductIds as $vk => $vv) {
                    foreach ($viewProducts['list'] as $vpk => $vpv) {
                        if ($vv == $vpv['id']) {
                            $viewProductList[$vv] = $vpv;
                            $viewProductList[$vv]['viewYmd'] = $views['data'][$vk]['create_time'];
                        }
                    }
                }
            }
            core_lib_Comm::p($viewProductList);
        }

        $this->_params["productList"] = $products['list'];
        $this->_params["pid"] = $parentCategoryId;
        $this->_params["viewList"] = $views['data'];
        return $this->render("product/index.html", $this->_params);
    }

    /**
     * 产品详情
     */
    public function pageDetail()
    {
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
        core_lib_Comm::p($productDes);

        //添加浏览记录
        $viewData['uuid'] = $this->_productViewLogQid;
        $viewData['product_id'] = $id;
        $dbViewHistory = new core_db_ViewHistory();
        $dbViewHistory->addViewHistory($viewData);

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
