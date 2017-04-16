<?php

/**
 * 产品相关
 */
class index_product extends index_base
{
    public function __construct()
    {
        parent::__construct();
        $this->_params['pageTitle'] = "产品管理";
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
        $cCategory = null;
        $childCategoryConditon = array("pid" => $parentCategoryId);
        $childCategorys = $dbCategory->queryAllCategory($childCategoryConditon, CATEGORY_SEL_NUM, 0);
        $cCategory = $childCategorys['items'];
        if ($childCategoryId > 0) {//二级类目
            $searchCategoryIds[] = $childCategoryId;
        } elseif ($parentCategoryId > 0) {//一级类目
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

        $this->pageBar($products['total'], $limit, $page, '/manager/list');

        //处理字段
        $columns = explode(",", PRODUCT_COLUMNS);
        $this->_params['cCategorys'] = $cCategory;
        $this->_params['products'] = $products['list'];
        $this->_params['pCategorys'] = $parentCategorys['items'];
        $this->_params['parentCategoryId'] = $parentCategoryId;//一级类目ID
        $this->_params['childCategoryId'] = $childCategoryId;//二级类目ID
        $this->_params['columns'] = $columns;
        $this->_params['actTitle'] = "产品列表";
        $this->_params['act'] = "productList";
//        return $this->render("boss/productList.html", $this->_params);
        return $this->render("boss/product/list.html", $this->_params);
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
        $isEdit = isset($_GET['isEdit']) ? core_lib_Comm::getStr($_GET['isEdit'], 'int') : 0;//是否编辑 1 编辑 0 添加
        $dbProduct = new core_db_Product();
        $dbProductDes = new core_db_ProductDes();
        $dbProductRelation = new core_db_ProductRelation();
        $msg = "添加";

        //查询一级类目
        $dbCategory = new core_db_Category();
        $categoryConditon = array("pid" => 0);
        $parentCategorys = $dbCategory->queryAllCategory($categoryConditon, CATEGORY_SEL_NUM, 0);

        if ($isEdit) {
            $productId = isset($_GET['productId']) ? core_lib_Comm::getStr($_GET['productId'], 'int') : 0;// 产品ID
            if (!$productId) {
                return $this->alert(array('status'=>'error','msg'=>"缺少管理员ID"));
            }
            $product = $dbProduct->getProductById($productId);//产品基础数据
            $productDes = $dbProductDes->getProductDesByProductId($productId);//产品描述
            $productRelations = $dbProductRelation->queryProductRelationList(array('proudct_id'=>$productId), CATEGORY_SEL_NUM, 0);

            $this->_params['product'] = $product;
            $this->_params['productDes'] = $productDes;
            $this->_params['productRelationList'] = $productRelations['list'];
            $msg = "编辑";
        }
        if ($_POST) {
            $brand = isset($_POST['brand']) ? core_lib_Comm::getStr($_POST['brand']) : '';//品牌
            $catalogNumber = isset($_POST['catalogNumber']) ? core_lib_Comm::getStr($_POST['catalogNumber']) : '';//货号
            $product = isset($_POST['product']) ? core_lib_Comm::getStr($_POST['product']) : '';//产品
            $package = isset($_POST['package']) ? core_lib_Comm::getStr($_POST['package']) : '';//包装
            $price = isset($_POST['price']) ? core_lib_Comm::getStr($_POST['price']) : '';//价格
            $abbreviation  = isset($_POST['abbreviation ']) ? core_lib_Comm::getStr($_POST['abbreviation ']) : '';//简写
            $chineseName = isset($_POST['chineseName']) ? core_lib_Comm::getStr($_POST['chineseName']) : '';//中文名
            $origin = isset($_POST['origin']) ? core_lib_Comm::getStr($_POST['origin']) : '';//产地
            $applicationProcess = isset($_POST['applicationProcess']) ? core_lib_Comm::getStr($_POST['applicationProcess']) : '';//应用/处理
            $otherName = isset($_POST[' otherName ']) ? core_lib_Comm::getStr($_POST['otherName']) : '';//别名
            $storageTemperature = isset($_POST['storageTemperature']) ? core_lib_Comm::getStr($_POST['storageTemperature']) : '';//储存温度
            $type = isset($_POST['type']) ? core_lib_Comm::getStr($_POST['type']) : '';//类别
            $raiseFrom = isset($_POST['raiseFrom']) ? core_lib_Comm::getStr($_POST['raiseFrom']) : '';//来源种属
            $reactsWith = isset($_POST['reactsWith']) ? core_lib_Comm::getStr($_POST['reactsWith']) : '';//反应种属
            $application = isset($_POST['application']) ? core_lib_Comm::getStr($_POST['application']) : '';//应用类别
            $label = isset($_POST['label']) ? core_lib_Comm::getStr($_POST['label']) : '';//标记物
            $casNo = isset($_POST['casNo']) ? core_lib_Comm::getStr($_POST['casNo']) : '';//CAS号
            $molecularFormula = isset($_POST['molecularFormula']) ? core_lib_Comm::getStr($_POST['molecularFormula']) : '';//分子式
            $molecularWeight = isset($_POST['molecularWeight']) ? core_lib_Comm::getStr($_POST['molecularWeight']) : '';//分子量
            $grade = isset($_POST['grade']) ? core_lib_Comm::getStr($_POST['grade']) : '';//级别
            $imgUrl = isset($_POST['imgUrl']) ? core_lib_Comm::getStr($_POST['imgUrl']) : '';//产品代表图片url
            $isSale = isset($_POST['isSale']) ? core_lib_Comm::getStr($_POST['isSale']) : '';//是否促销产品
            $categoryId = isset($_POST['categoryId']) ? core_lib_Comm::getStr($_POST['categoryId']) : '';//类目ID
            $productDes = isset($_POST['productDes']) ? core_lib_Comm::getStr($_POST['productDes']) : '';//产品描述
            $productRelations = isset($_POST['productRelations']) ? core_lib_Comm::getStr($_POST['productRelations']) : '';//产品关联文件
            $productRelationsType = isset($_POST['productRelationsType']) ? core_lib_Comm::getStr($_POST['productRelationsType']) : '';//产品关联文件类型
            $productRelationsTitle = isset($_POST['productRelationsTitle']) ? core_lib_Comm::getStr($_POST['productRelationsTitle']) : '';//产品关联文件标题
            $productRelationsPath = isset($_POST['productRelationsPath']) ? core_lib_Comm::getStr($_POST['productRelationsPath']) : '';//产品关联文件路径

            $data['brand'] = $brand;
            $data['catalog_number'] = $catalogNumber;
            $data['product'] = $product;
            $data['package'] = $package;
            $data['price'] = $price;
            $data['abbreviation'] = $abbreviation;
            $data['chinese_name'] = $chineseName;
            $data['origin'] = $origin;
            $data['application_process'] = $applicationProcess;
            $data['other_name'] = $otherName;
            $data['storage_temperature'] = $storageTemperature;
            $data['type'] = $type;
            $data['raise_from'] = $raiseFrom;
            $data['reacts_with'] = $reactsWith;
            $data['application'] = $application;
            $data['label'] = $label;
            $data['case_no'] = $casNo;
            $data['molecular_formula'] = $molecularFormula;
            $data['molecular_weight'] = $molecularWeight;
            $data['grade'] = $grade;
            $data['img_url'] = $imgUrl;
            $data['is_sale'] = $isSale;
            $data['category_id'] = $categoryId;

            if ($isEdit) {
                $data['product_id'] = $productId;
                //编辑基础产品信息
                $productRS = $dbProduct->updateProductById($data);
                if ($productRS === false) {
                    return $this->alert(array('status'=>'error','msg'=>"产品编辑失败"));
                }

                //编辑产品描述
                $productDesData['product_id'] = $productId;
                $productDesData['description'] = $productDes;
                $productDesRS = $dbProductDes->updateProductDesByProductId($productDesData);
                if ($productDesRS === false) {
                    return $this->alert(array('status'=>'error','msg'=>"产品描述编辑失败"));
                }

                //添加编辑管理关联文件
                if ($productRelations) {
                    //先删除
                    $dbProductRelation->deleteProductRelationByProductId($productId);
                    foreach ($productRelations as $prk => $prv) {
                        $productRelationData['product_id'] = $productId;
                        $productRelationData['type'] = $productRelationsType[$prk];
                        $productRelationData['title'] = $productRelationsTitle[$prk];
                        $productRelationData['download_url'] = $productRelationsPath[$prk];
                        $dbProductRelation->addProductRelation($productRelationData);
                    }
                }

            } else {
                //添加基础产品信息
                $productId = $dbProduct->addProduct($data);
                if ($productId === false) {
                    return $this->alert(array('status'=>'error','msg'=>"产品添加失败"));
                }

                //添加产品描述
                $productDesData['product_id'] = $productId;
                $productDesData['description'] = $productDes;
                $productDesRS = $dbProductDes->addProductDes($productDesData);
                if ($productDesRS === false) {
                    return $this->alert(array('status'=>'error','msg'=>"产品描述添加失败"));
                }

                //添加产品管理关联文件
                if ($productRelations) {
                    foreach ($productRelations as $prk => $prv) {
                        $productRelationData['product_id'] = $productId;
                        $productRelationData['type'] = $productRelationsType[$prk];
                        $productRelationData['title'] = $productRelationsTitle[$prk];
                        $productRelationData['download_url'] = $productRelationsPath[$prk];
                        $dbProductRelation->addProductRelation($productRelationData);
                    }
                }

            }
            return $this->alert(array('status'=>'error','msg'=>$msg."成功"));
        }
        $this->_params['isEdit'] = $isEdit;
        $this->_params['parentCategorys'] = $parentCategorys['items'];
        $this->_params['actTitle'] = $msg."产品";
        $this->_params['act'] = "productList";
        return $this->render("boss/product/action.html", $this->_params);
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

        //产品描述文档
        $dbProductDes = new core_db_ProductDes();
        $des = $dbProductDes->getProductDesByProductId($productId);

 //       $dbProductRel = new core_db_ProductRelation();
        //$rel = $dbProductRel->getOneProductRelByProductId($productId);//产品关联内容

        $param["product"] = $product;
        $param["des"] = $des;
///        $param["rel"] = $rel;
        return $this->render("boss/productdetail.html", $param);
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
