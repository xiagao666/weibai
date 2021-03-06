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
    public function pageList()
    {
        $parentCategoryId = isset($_GET["parentCategoryId"]) ? core_lib_Comm::getStr($_GET["parentCategoryId"],
            'int') : 0;//父ID
        $childCategoryId = isset($_GET["childCategoryId"]) ? core_lib_Comm::getStr($_GET["childCategoryId"],
            'int') : 0;//子ID
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 30;
        $isSale = isset($_GET['isSale']) ? core_lib_Comm::getStr($_GET["isSale"], 'int') : 2;
        //查询一级类目
        $dbCategory = new core_db_Category();
        $categoryConditon = array("pid" => 0);
        $parentCategorys = $dbCategory->queryAllCategory($categoryConditon, CATEGORY_SEL_NUM, 0);
        $cCategory = null;
        if($parentCategoryId > 0){
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
        }
        $searchKey = isset($_GET['searchKey']) ? core_lib_Comm::getStr($_GET['searchKey']) : '';
        $searchValue = isset($_GET['searchVal']) ? core_lib_Comm::getStr($_GET['searchVal']) : '';
        $query = "";
        if($isSale < 2 && $isSale >= 0){
            $query['is_sale'] = $isSale;
        }
        if(!empty($searchKey) && !empty($searchValue)) {
            $query[] = $searchKey." like '%".$searchValue."%'";
        }

        $searchCategoryStr = is_array($searchCategoryIds) ? implode(",", $searchCategoryIds) : '';
        if (is_array($searchCategoryIds)) {
            $query[] = "category_id in ({$searchCategoryStr})";
        }
        $dbProduct = new core_db_Product();
        $products = $dbProduct->queryProductList($query, array("id" => "desc"), $limit, $page);

        $categorys = $dbCategory->queryAllCategory(array("pid != 0"), 10000, 0);
        if ($categorys['items']) {
            foreach ($categorys['items'] as $csk => $csv) {
                $categoryList[$csv['id']] = $csv;
            }
        }
        if ($products['list']) {
            foreach ($products['list'] as $podk => $podv) {
                $products['list'][$podk]['category'] = $categoryList[$podv['category_id']]['name'];
            }
        }

        $this->pageBar($products['total'], $limit, $page, '/product/list');

        //处理字段
        $columns = core_lib_Comm::getTableColumns(PRODUCT_COLUMNS);
        $selColumns = array_values($columns);//筛选
        $columns['category'] = "分类";
        $this->_params['cCategorys'] = $cCategory;
        $this->_params['products'] = $products['list'];
        $this->_params['pCategorys'] = $parentCategorys['items'];
        $this->_params['parentCategoryId'] = $parentCategoryId;//一级类目ID
        $this->_params['childCategoryId'] = $childCategoryId;//二级类目ID
        $this->_params['columns'] = $columns;//显示字段
        $this->_params['selColumns'] = $selColumns;
        $this->_params['actTitle'] = "产品列表";
        $this->_params['act'] = "productList";
        $this->_params['searchKey'] = $searchKey;
        $this->_params['searchVal'] = $searchValue;
        $this->_params['isSale'] = $isSale;
        return $this->render("products/list.html", $this->_params);
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

            $dbCategory = new core_db_Category();
            //表名作为一级类目名称
            $excelName = trim($excel['name']);
            $parent = explode(".", $excelName);
            $parentCategory = $dbCategory->getCategoryByName($parent[0]);

            $categoryNames = array_column($products, 'category');
            $categoryNames = array_unique($categoryNames);
            if (!empty($categoryNames)) {
                foreach ($categoryNames as $ck => $cv) {
                    $query = array();
                    $query['name'] = $cv;
                    $query['pid'] = $parentCategory['id'];
                    $categoryList = $dbCategory->queryAllCategory($query, 1, 1);
                    $categorys[] = $categoryList['items'][0];
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
                        if (trim($pv['category']) == $nv) {
                            $categoryId = $categoryIds[$nk];
                        }
                    }
                    unset($pv['category']);
                    if (!$pv['catalog_number'] || !$pv['package']) {
                        $errorData[] = $pv;
                        continue;
                    }

                    $hasQueryProduct = array('catalog_number'=>$pv['catalog_number'], 'package'=>$pv['package']);
                    $hasProduct = $dbProduct->queryProductList($hasQueryProduct, 1, 1);

                    if ($hasProduct) {//已有产品 做更新处理
                        $updateProduct = array_filter($pv);
                        if (is_array($updateProduct)) {
                            foreach ( $updateProduct as $ik => $iv) {
                                unset($updateProduct[$ik]);
                                $updateProduct[trim($ik)] = trim($iv);
                            }
                        }
                        $updateProduct['product_id'] = $hasProduct['list'][0]['id'];
                        $updateProduct['category_id'] = $categoryId;
                        $productRS = $dbProduct->updateProductById($updateProduct);
                        if ($productRS) {
                            $updateCount++;
                        } else {
                            $updateError[] = $updateProduct;
                        }
                    } else {
                        $importProduct = array_filter($pv);
                        if (is_array($importProduct)) {
                            foreach ( $importProduct as $ik => $iv) {
                                unset($importProduct[$ik]);
                                $importProduct[trim($ik)] = trim($iv);
                            }
                        }
                        $importProduct['category_id'] = $categoryId;
                        $importProduct['img_url'] = WWW_URL.'/pdimage/productDefault.jpg';

                        $productRS = $dbProduct->addProduct($importProduct);
                        if ($productRS) {
                            $insertCount++;
                        } else {
                            $insertError[] = $importProduct;
                        }
                    }
                }
                //没有货号和规格/包装
                if (is_array($errorData)) {
                    $message = "导入数据中没有货号和规格/包装的数据：\n";
                    foreach ($errorData as $ek => $ev) {
                        $i=0;
                        foreach ($ev as $eek => $eev) {
                            $message .= $eek."：".$eev."，";
                            if ($i == count($eev)) {
                                $message .= "\n";
                            }
                            $i++;
                        }
                    }
                }
                if (is_array($updateError)) {
                    $message .= "\n更新失败的数据有：\n";
                    foreach ($updateError as $uk => $uv) {
                        $i=0;
                        foreach ($uv as $uuk => $uuv) {
                            $message .= $uuk."：".$uuv."，";
                            if ($i == count($uuv)) {
                                $message .= "\n";
                            }
                            $i++;
                        }
                    }
                }
                if (is_array($insertError)) {
                    $message .= "\n添加失败的数据有：\n";
                    foreach ($insertError as $iek => $iev) {
                        $i=0;
                        foreach ($iev as $iiek => $iiev) {
                            $message .= $iiek."：".$iiev."，";
                            if ($i == count($iiev)) {
                                $message .= "\n";
                            }
                            $i++;
                        }
                    }
                }
                mail(TOMAIL, "产品导入错误提示", $message, FROMMAIL);
                if ($productRS == true) {
                    return $this->alert(array('status'=>'success','msg'=>"成功更新{$updateCount}条产品，成功添加{$insertCount}条产品"));
                }
            }
            //处理失败提示
            return $this->alert(array('status'=>'error','msg'=>"失败"));
        } else {
            return $this->alert(array('status'=>'error','msg'=>"导入姿势不对"));
        }
    }

    /**
     * 添加／编辑产品
     */
    public function pageAction()
    {
        $isEdit = isset($_REQUEST['isEdit']) ? core_lib_Comm::getStr($_REQUEST['isEdit'], 'int') : 0;//是否编辑 1 编辑 0 添加
        $productId = isset($_REQUEST['productId']) ? core_lib_Comm::getStr($_REQUEST['productId'], 'int') : 0;// 产品ID
        $dbProduct = new core_db_Product();
        $dbProductDes = new core_db_ProductDes();
        $dbProductRelation = new core_db_ProductRelation();
        $msg = "添加";

        //查询一级类目
        $dbCategory = new core_db_Category();
        $categoryConditon = array("pid" => 0);
        $pCategorys = $dbCategory->queryAllCategory($categoryConditon, CATEGORY_SEL_NUM, 1);

        $reference = array(
            array(
                'id' => 1,
                'name'=>'文献1',
                'referenceDes'=>'',
                'referenceUrl'=>''
            ),
            array(
                'id' => 2,
                'name'=>'文献2',
                'referenceDes'=>'',
                'referenceUrl'=>''
            ),
            array(
                'id' => 3,
                'name'=>'文献3',
                'referenceDes'=>'',
                'referenceUrl'=>''
            ),
            array(
                'id' => 4,
                'name'=>'文献4',
                'referenceDes'=>'',
                'referenceUrl'=>''
            ),
            array(
                'id' => 5,
                'name'=>'文献5',
                'referenceDes'=>'',
                'referenceUrl'=>''
            )
        );

        if ($isEdit) {
            if (!$productId) {
                return $this->alert(array('status'=>'error','msg'=>"缺少管理员ID"));
            }
            $product = $dbProduct->getProductById($productId);//产品基础数据
            $productDesInfo = $dbProductDes->getProductDesByProductId($productId);//产品描述
            $productRelations = $dbProductRelation->queryProductRelationList(array('product_id'=>$productId), CATEGORY_SEL_NUM, 1);

            if ($product['category_id']) {
                $productCategory = $dbCategory->getCategoryById($product['category_id']);
                $this->_params['parentCategoryId'] = $productCategory['pid'];
                if ($productCategory['pid']) {
                    $childCategorys = $dbCategory->queryAllCategory(array('pid'=>$productCategory['pid']), CATEGORY_SEL_NUM, 1);
                    $this->_params['cCategorys'] = $childCategorys['items'];
                }
            }

            if ($productRelations['list']) {// 文献、文章type=1/产品说明书type=2
                foreach ($productRelations['list'] as $prek => $prev) {
                    if ($prev['type'] == 1) {
                        $reference[$prek]['referenceDes'] = $prev['title'];
                        $reference[$prek]['referenceUrl'] = $prev['hyper_link'];
                    } else {
                        $productRelationList[$prev['type']][] = $prev;
                    }
                }
            }

            $this->_params['product'] = $product;
            $this->_params['productDes'] = $productDesInfo;
            $this->_params['productRelationList'] = $productRelationList;
            $msg = "编辑";
        }

        if ($_POST) {
            $brand = isset($_POST['brand']) ? core_lib_Comm::getStr($_POST['brand']) : '';//品牌
            $catalogNumber = isset($_POST['catalogNumber']) ? core_lib_Comm::getStr($_POST['catalogNumber']) : '';//货号
            $product = isset($_POST['product']) ? core_lib_Comm::getStr($_POST['product']) : '';//产品
            $package = isset($_POST['package']) ? core_lib_Comm::getStr($_POST['package']) : '';//包装
            $price = isset($_POST['price']) ? core_lib_Comm::getStr($_POST['price']) : '';//价格
            $abbreviation  = isset($_POST['abbreviation']) ? core_lib_Comm::getStr($_POST['abbreviation']) : '';//简写
            $chineseName = isset($_POST['chineseName']) ? core_lib_Comm::getStr($_POST['chineseName']) : '';//中文名
            $origin = isset($_POST['origin']) ? core_lib_Comm::getStr($_POST['origin']) : '';//产地
            $applicationProcess = isset($_POST['applicationProcess']) ? core_lib_Comm::getStr($_POST['applicationProcess']) : '';//应用/处理
            $otherName = isset($_POST['otherName']) ? core_lib_Comm::getStr($_POST['otherName']) : '';//别名
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
            $categoryId = isset($_POST['childCategoryId']) ? core_lib_Comm::getStr($_POST['childCategoryId']) : '';//类目ID
            $productDes = isset($_POST['productDes']) ? core_lib_Comm::reMoveXss($_POST['productDes']) : '';//产品描述
            $sort = isset($_POST['sort']) ? core_lib_Comm::reMoveXss($_POST['sort']) : '';//排序

            $productRelationsTitle = isset($_POST['productRelationsTitle']) ? core_lib_Comm::getStr($_POST['productRelationsTitle']) : '';//产品关联文件标题
            $productRelationsPath = isset($_POST['productRelationsPath']) ? core_lib_Comm::getStr($_POST['productRelationsPath']) : '';//产品关联文件路径
            $referenceDes = isset($_POST['referenceDes']) ? core_lib_Comm::getStr($_POST['referenceDes']) : '';//产品文献描述
            $referenceUrl = isset($_POST['referenceUrl']) ? core_lib_Comm::getStr($_POST['referenceUrl']) : '';//产品文献Url
            if (!$catalogNumber || !$package) {
                return $this->alert(array('status'=>'error','msg'=>"产品货号或包装不能为空"));
            }

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
            $data['cas_no'] = $casNo;
            $data['molecular_formula'] = $molecularFormula;
            $data['molecular_weight'] = $molecularWeight;
            $data['grade'] = $grade;
            $data['img_url'] = $imgUrl[0] ? $imgUrl[0] : WWW_URL.'/pdimage/productDefault.jpg';
            $data['is_sale'] = $isSale;
            $data['category_id'] = $categoryId;
            $data['sort'] = $sort;

            //根据货号查询 不同规格的产品
            $tegQueryData['catalog_number'] = $catalogNumber;
            $tegProducts = $dbProduct->queryProductList(array('catalog_number'=>$catalogNumber), array("sort"=>"desc","id"=>"desc"), 1000, 1);
            if (is_array($tegProducts['list'])) {
                foreach ($tegProducts['list'] as $tk => $tv) {
                    if ($tv['id'] != $productId) {
                        $tegUpdateData['product_id'] = $tv['id'];
                        $tegUpdateData['img_url'] = $data['img_url'];
                        $dbProduct->updateProductById($tegUpdateData);
                    }
                }
            }

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
                if ($productDesInfo === false ) {
                    $productDesRS = $dbProductDes->addProductDes($productDesData);
                } else {
                    $productDesRS = $dbProductDes->updateProductDesByProductId($productDesData);
                }
                if ($productDesRS === false) {
                    return $this->alert(array('status'=>'error','msg'=>"产品描述编辑失败"));
                }

                //添加编辑管理关联文件
                $dbProductRelation->deleteProductRelationByProductId($productId);
                if (is_array($productRelationsPath)) {
                    //先删除
                    foreach ($productRelationsPath as $prk => $prv) {
                        foreach ($prv as $cprk => $cprv) {
                            $productRelationData['product_id'] = $productId;
                            $productRelationData['type'] = $prk;
                            $productRelationData['title'] = $productRelationsTitle[$prk][$cprk];
                            $productRelationData['download_url'] = $cprv;
                            $dbProductRelation->addProductRelation($productRelationData);
                        }
                    }
                }
                //产品文献
                if ($referenceDes) {
                    foreach ($referenceDes as $rk => $rv) {
                        if ($rv) {
                            $referenceData['product_id'] = $productId;
                            $referenceData['type'] = 1;
                            $referenceData['title'] = $rv;
                            $referenceData['hyper_link'] = $referenceUrl[$rk];
                            $dbProductRelation->addProductRelation($referenceData);
                        }
                    }
                }
                return $this->alert(array('status'=>'success','msg'=>$msg."成功"));
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
                if ($productRelationsPath) {
                    foreach ($productRelationsPath as $prk => $prv) {
                        foreach ($prv as $cprk => $cprv) {
                            $productRelationData['product_id'] = $productId;
                            $productRelationData['type'] = $prk;
                            $productRelationData['title'] = $productRelationsTitle[$prk][$cprk];
                            $productRelationData['download_url'] = $cprv;
                            $dbProductRelation->addProductRelation($productRelationData);
                        }
                    }
                }
                //产品文献
                if ($referenceDes) {
                    foreach ($referenceDes as $rk => $rv) {
                        if ($rv) {
                            $referenceData['product_id'] = $productId;
                            $referenceData['type'] = 1;
                            $referenceData['title'] = $rv;
                            $referenceData['hyper_link'] = $referenceUrl[$rk];
                            $dbProductRelation->addProductRelation($referenceData);
                        }
                    }
                }
            }
            return $this->alert(array('status'=>'success','msg'=>$msg."成功"));
        }
        $this->_params['isEdit'] = $isEdit;
        $this->_params['pCategorys'] = $pCategorys['items'];
        $this->_params['actTitle'] = $msg."产品";
        $this->_params['act'] = "productList";
        $this->_params['productId'] = $productId;
        $this->_params['reference'] = $reference;
        return $this->render("products/action.html", $this->_params);
    }

    /**
     * 删除产品
     */
    public function pageDelProduct()
    {
        if ($_POST) {
            $productId = isset($_REQUEST['productId']) ? core_lib_Comm::getStr($_REQUEST['productId'], 'int') : 0;// 产品ID

            $dbProduct = new core_db_Product();
            $dbProductDes = new core_db_ProductDes();
            $dbProductRelation = new core_db_ProductRelation();
            $product = $dbProduct->getProductById($productId);
            if ($product == false) {
                return $this->alert(array('status'=>'error','msg'=>"删除的产品不存在"));
            }

            $dbProductDes->deleteProductDesByProductId($productId);
            $dbProductRelation->deleteProductRelationByProductId($productId);
            $productRS = $dbProduct->deleteProductById($productId);
            if ($productRS === false) {
                return $this->alert(array('status'=>'error','msg'=>"删除失败"));
            }
            return $this->alert(array('status'=>'success','msg'=>"删除成功"));
        } else {
            return $this->alert(array('status'=>'error','msg'=>"姿势没摆对"));
        }
    }
}
