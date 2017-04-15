<?php

/**
 * 产品相关
 */
class index_product extends index_base
{
    public function __construct()
    {
//        parent::__construct();
    }

    /**
     * 首页
     */
    public function pageList($inPath)
    {
        $parentCategoryId = isset($_GET["parentCategoryId"]) ? core_lib_Comm::getStr($_GET["parentCategoryId"],
            'int') : 0;//父ID
        $childCategoryId = isset($_GET["childCategoryId"]) ? core_lib_Comm::getStr($_GET["childCategoryId"],
            'int') : 0;//子ID
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 10;

        //查询一级类目
        $dbCategory = new core_db_Category();
        $categoryConditon = array("pid" => 0);
        $parentCategorys = $dbCategory->queryAllCategory($categoryConditon, CATEGORY_SEL_NUM, 0);

        if ($childCategoryId > 0) {//二级类目
            $searchCategoryIds[] = $childCategoryId;
        } elseif ($parentCategoryId > 0) {//一级类目
            $childCategoryConditon = array("pid" => $parentCategoryId);
            $childCategorys = $dbCategory->queryAllCategory($childCategoryConditon, CATEGORY_SEL_NUM, 0);
            if ($childCategorys['items']) {
                foreach ($childCategorys['items'] as $ck => $cv) {
                    $searchCategoryIds[] = $cv['id'];
                }
            }
        }
        $searchCategoryStr = is_array($searchCategoryIds) ? implode(",", $searchCategoryIds) : '';
        if (is_array($searchCategoryIds)) {
            $query = "category_id in ({$searchCategoryStr})";
        }

        $dbProduct = new core_db_Product();
        $products = $dbProduct->queryProductList($query, $page, $limit, array("id" => "desc"));

        //处理字段
        $columns = explode(",", PRODUCT_COLUMNS);

        $params['total'] = $products['total'];
        $params['products'] = $products['list'];
        $params['limit'] = $limit;
        $params['page'] = $page;
        $params['parentCategorys'] = $parentCategorys['items'];
        $params['parentCategoryId'] = $parentCategoryId;//一级类目ID
        $params['childCategoryId'] = $childCategoryId;//二级类目ID
        $params['columns'] = $columns;
        return $this->render("boss/productList.html", $params);
    }

    /**
     * 导入商品
     */
    public function pageImport()
    {
        if ($_POST) {
            $excel = $_FILES['excel'];
            $SExcel = new SExcel();
            $data = $SExcel->importExcel($excel['tmp_name']);
            $products = array_filter($data);
            unset($products[0]);

            $categoryNames = array_column($products, 'category_id');
            $categoryNames = array_unique($categoryNames);
            if (!empty($categoryNames)) {
                $dbCategory = new core_db_Category();
                foreach ($categoryNames as $ck => $cv) {
                    $categorys[] = $dbCategory->getCategoryByName($cv);
                }
            }
            $categoryIds = array_column($categorys, 'id');
            $newCategoryNames = array_column($categorys, 'name');

            $dbProduct = new core_db_Product();
            if ($products) {
                //删除空数据 判断是否之前添加过相同产品
                foreach ($products as $pk => $pv) {
                    $updateCount = 0;
                    $insertCount = 0;
                    $updateProduct = array();
                    $importProduct = array();
                    foreach ($newCategoryNames as $nk => $nv) {
                        if (trim($pv['category_id']) == $nv) {
                            $categoryId = $categoryIds[$nk];
                        }
                    }
                    $hasQueryProduct = array('catalog_number'=>$pv['catalog_number'], 'package'=>$pv['package']);
                    $hasProduct = $dbProduct->queryProductList($hasQueryProduct, 1, 0);
                    if ($hasProduct) {//已有产品 做更新处理
                        $updateProduct = array_filter($pv);;
                        $updateProduct['id'] = $hasProduct['list'][0]['id'];
                        $updateProduct['category_id'] = $categoryId;
                        $productRS = $dbProduct->updateProductById($updateProduct);
                        if ($productRS) {
                            $updateCount++;
                        } else {
                            $updateError[] = $updateProduct;
                        }
                    } else {
                        $importProduct = array_filter($pv);
                        $importProduct['category_id'] = $categoryId;
                        $productRS = $dbProduct->addProduct($importProduct);
                        if ($productRS) {
                            $insertCount++;
                        } else {
                            $insertError[] = $updateProduct;
                        }
                    }
                }
                //@todo 发送邮件 处理失败的数据发送给 导入的人
                if ($productRS == true) {
                    return $this->alert(array('status'=>'success','msg'=>"成功处理"));
                }
            }
            //处理失败提示
            return $this->alert(array('status'=>'error','msg'=>"失败"));
        }
    }

    /**
     * 添加／编辑产品
     */
    public function pageAction()
    {

    }

    /**
     * 产品明细信息
     */
    public function pageDetail($inPath)
    {
        $productId = isset($_GET["id"]) ? core_lib_Comm::getStr($_GET["id"], 'int') : 0;
        if ($productId) {
//            return $this->alert(array('status'=>'error','msg'=>"缺少产品编号"));
            core_lib_Comm::p('缺少产品编号');
        }

        //产品详细信息
        $dbProduct = new core_db_Product();
        $product = $dbProduct->getProductById($productId);
        core_lib_Comm::p($product);

        //产品描述文档
        $dbProductDes = new core_db_ProductDes();
        $des = $dbProductDes->getProductDesByProductId($productId);

        $dbProductRel = new core_db_ProductRelation();
//        $rel = $dbProductRel->getOneProductRelByProductId($productId);//产品关联内容
//
        $param["product"] = $product;
        $param["des"] = $des;
//        $param["rel"] = $rel;
        echo json_encode($param);
    }


    //产品列表
    public function pageGetList($inPath)
    {
        $categoryParentId = $_GET["categoryParentId"];//父ID
        $categoryChildId = $_GET["categoryChildId"];//子ID
        if ($categoryChildId == 0 && $categoryParentId > 0) {
            $dbCategory = new core_db_Category();
            $condition['pid'] = $categoryParentId;
            $rs = $dbCategory->queryAllCategory($condition);
            if ($rs['items']) {
                foreach ($rs['items'] as $v) {
                    $ids[] = $v['id'];
                }
            }
        }
        if ($categoryChildId > 0) {
            $ids[] = $categoryChildId;
        }
        $category = explode(",", $ids);
        $page = $_GET["page"];
        $limit = $_GET["limit"];
        if (!empty($ids)) {
            $query = array("category_id in ({$category})");
        }
        $dbProduct = new core_db_Product();
        $productRs = $dbProduct->queryProductList($query, $page, $limit, "id desc");
        $param['items'] = $productRs->items;
        $page['total'] = $productRs->totalSize;
        $param['limit'] = $limit;
        $param['page'] = $page;
        $param['categoryParentId'] = $categoryParentId;
        $param['categoryChildId'] = $categoryChildId;
        $param['cCategory'] = $rs['items'];
        echo json_encode($productRs);
    }



}
