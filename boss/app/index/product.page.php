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
        $parentCategoryId = isset($_GET["parentCategoryId"]) ? core_lib_Comm::getStr($_GET["parentCategoryId"], 'int') : 0;//父ID
        $childCategoryId = isset($_GET["childCategoryId"]) ? core_lib_Comm::getStr($_GET["childCategoryId"], 'int') : 0;//子ID
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 10;

        //查询一级类目
        $dbCategory = new core_db_Category();
        $categoryConditon = array("pid"=>0);
        $parentCategorys = $dbCategory->queryAllCategory($categoryConditon);
//        core_lib_Comm::p($parentCategorys['items']);exit;
        $searchCategoryIds = null;
        $childCategorys = null;
        if ($childCategoryId > 0) {//二级类目
            $searchCategoryIds[] = $childCategoryId;
        } elseif ($parentCategoryId > 0) {//一级类目
            $childCategoryConditon = array("pid"=>$parentCategoryId);
            $childCategorys = $dbCategory->queryAllCategory($childCategoryConditon);
            if ($childCategorys['items']) {
                foreach ($childCategorys['items'] as $ck => $cv) {
                    $searchCategoryIds[] = $cv['id'];
                }
            }
        }

        $params['cCategorys'] = $childCategorys['items'];
        $searchCategoryStr = is_array($searchCategoryIds)  ? implode(",", $searchCategoryIds) : '';
        $query = "";
        if (is_array($searchCategoryIds)) {
            $query = "category_id in ({$searchCategoryStr})";
        }

        $dbProduct = new core_db_Product();
        $products = $dbProduct->queryProductList($query, $page, $limit, array("id"=>"desc"));
        //core_lib_Comm::p($products);

        $params['total'] = $products['total'];
        $params['productList'] = $products['items'];
        $params['limit'] = $limit;
        $params['page'] = $page;
        $params['pCategorys'] = $parentCategorys['items'];
        $params['parentCategoryId'] = $parentCategoryId;//一级类目ID
        $params['childCategoryId'] = $childCategoryId;//二级类目ID
        $params["columns"] = core_lib_Comm::getTableColumns(PRODUCT_COLUMNS);
        //core_lib_Comm::p($params);
        return $this->render("boss/productList.html", $params);
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
        $param['cCategory'] = $rs['items'];
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
