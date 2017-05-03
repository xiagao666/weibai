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
        $childCategoryId = isset($_GET['cid']) ? core_lib_Comm::getStr($_GET["cid"]) : 0;//子级ID
        $limit = 3;
        //查询产品信息
        $dbProduct = new core_db_Product();
        if ($parentCategoryId && !$childCategoryId) {
            $dbCategory = new core_db_Category();
            $categoryIds = $dbCategory->queryAllCategory(array('pid'=>$parentCategoryId), 1000, 1);
            $childCategoryIds = array_column($categoryIds['items'], 'id');
            if (is_array($childCategoryIds)) {
                $query[] = "category_id in ".implode(",", $childCategoryIds);
            }
        }
        if ($childCategoryId) {
            $query['category_id'] = $childCategoryId;
        }
        if ($key) {
            $query[] = "catalog_number like '%{$key}%' OR product like '%{$key}%' OR abbreviation like '%{$key}%' OR chinese_name like '%{$key}%' OR other_name like '%{$key}%'";
        }

        $products = $dbProduct->queryProductList($query, array("sort"=>"desc"), $limit, $page);
        $totalPage = ceil($products['total']/$limit);

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
        }

        if ($key) {
            $urlData['key'] = $key;
        }
        if ($parentCategoryId) {
            $urlData['pid'] = $parentCategoryId;
        }
        if ($parentCategoryId) {
            $urlData['cid'] = $childCategoryId;
        }
        $urlData['page'] = '';
        $url = '/product/index?'.http_build_query($urlData);

        $this->_params["productList"] = $products['list'];
        $this->_params["pid"] = $parentCategoryId;
        $this->_params["viewList"] = $views['data'];
        $this->_params["page"] = $page;
        $this->_params["totalPage"] = $totalPage;
        $this->_params["key"] = $key;
        $this->_params["parentCategoryId"] = $parentCategoryId;
        $this->_params["childCategoryId"] = $childCategoryId;
        $this->_params["url"] = $url;
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
        if ($product === false) {
            return $this->alert(array("status"=>"error", "msg"=>"打开的产品不存在"));
        }

        //获取分类信息
        $dbCategory = new core_db_Category();
        $category = $dbCategory->getCategoryById($product['category_id']);
        $pCategory = $dbCategory->getCategoryById($category['pid']);

        $dbProductDes = new core_db_ProductDes();
        $productDes = $dbProductDes->getProductDesByProductId($id);

        $dbProductRelation = new core_db_ProductRelation();
        $condition["product_id"] = $id;
        $productRelations = $dbProductRelation->queryProductRelationList($condition);
        if ($productRelations['list']) {// 文献、文章type=1/产品说明书type=2
            foreach ($productRelations['list'] as $prek => $prev) {
                $productRelationList[$prev['type']][] = $prev;
            }
        }

        //添加浏览记录
        $viewData['uuid'] = $this->_productViewLogQid;
        $viewData['product_id'] = $id;
        $dbViewHistory = new core_db_ViewHistory();
        $dbViewHistory->addViewHistory($viewData);

        $this->_params["product"] = $product;
        $this->_params["pCategory"] = $pCategory;
        $this->_params["category"] = $category;
        $this->_params["productDes"] = $productDes;
        $this->_params['productRelationList'] = $productRelationList;
        $this->render("productDetail/index.html", $this->_params);
    }
}
