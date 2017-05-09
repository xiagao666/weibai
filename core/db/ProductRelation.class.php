<?php
/**
 * 产品文件相关
 */
class core_db_ProductRelation extends core_db_DbBase {

    private $table = "vb_product_relation";

    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * 添加产品关联文件
     * @param $data
     * @return bool
     */
    public function addProductRelation($data) {
        try{
            $productId = $data['product_id'];
            if(empty($data) || !$productId) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common","main");
            $rs = $this->insertData($data);
            if($rs === false) {
                throw new Exception("添加记录失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 条件查询产品文件列表
     */
    public function queryProductRelationList($param, $limit = 10, $page = 0){
        try{
            $this->useConfig("common","query");
            $productRelations = $this->getAllData($param,'*', "", "", "", $limit, $page);
            if ($productRelations === false) {
                throw new Exception("查询失败或无相关文件");
            }
            $productRelationList['total'] = $productRelations->total;
            $productRelationList['list'] = $productRelations->items;
            return $productRelationList;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 文件ID查文件
     * @param $productId
     */
    public function getProductRelationsById($id) {
        try{
            if (!$id) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common","query");
            $condition["id"] = $id;
            $productRelation = $this->getOne($condition, '*');
            if ($productRelation === false) {
                throw new Exception("查询失败或无相关文件");
            }
            return $productRelation;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 文件ID删除关联产品ID
     * @param $condition
     * @return bool
     */
    public function deleteProductRelationById($id) {
        try{
            if(!$id) {
                throw new Exception("缺少必要参数");
            }

            $productRelation = $this->getProductRelationsById($id);
            if ($productRelation === false) {
                throw new Exception("查询失败或无相关文件");
            }

            $this->useConfig("common","main");
            $productRS = $this->deleteData(array('id'=>$id));
            if($productRS === false) {
                throw new Exception("删除失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 产品ID删除关联产品ID
     * @param $condition
     * @return bool
     */
    public function deleteProductRelationByProductId($productId) {
        try{
            if(!$productId) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common","main");
            $productRS = $this->deleteData(array('product_id'=>$productId));
            if($productRS === false) {
                throw new Exception("删除失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }
}