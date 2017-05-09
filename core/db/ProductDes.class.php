<?php
/**
 * 产品描述相关
 */
class core_db_ProductDes extends core_db_DbBase {
    private $table = "vb_product_description";

    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * 添加描述
     * @param $data
     * @return bool
     */
    public function addProductDes($data) {
        try{
            $productId = $data['product_id'];
            if(empty($data) || !$productId) {
                throw new Exception("缺少必要参数");
            }

            //判断描述是否添加过
            $productDes = $this->getProductDesByProductId($productId);

            if ($productDes) {
                throw new Exception("产品描述不能重复添加");
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
     * 更新产品描述
     * @param $condition
     * @param $item
     * @return bool
     */
    public function updateProductDesByProductId($data){
        try{
            $productId = $data['product_id'];
            if(empty($data) || !$productId) {
                throw new Exception("缺少必要参数");
            }

            //判断描述是否添加过
            $productDes = $this->getProductDesByProductId($productId);
            if (!$productDes) {
                throw new Exception("更新产品描述不存在");
            }

            $this->useConfig("common","main");
            $rs = $this->updateData(array('product_id'=>$productId), $data);
            if($rs === false) {
                throw new Exception("更新失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 删除产品描述
     * @param $condition
     * @return bool
     */
    public function deleteProductDesByProductId($productId) {
        try{
            if(!$productId) {
                throw new Exception("缺少必要参数");
            }

            //判断描述是否添加过
            $productDes = $this->getProductDesByProductId($productId);
            if (!$productDes) {
                throw new Exception("删除产品描述不存在");
            }

            $this->useConfig("common","main");
            $rs = $this->deleteData(array("product_id"=>$productId));
            if($rs === false) {
                throw new Exception("删除失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 产品ID查产品描述
     * @param $productId
     * @return bool
     */
    public function getProductDesByProductId($productId) {
        try{
            if(!$productId) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common","query");
            $condition = array('product_id'=>$productId);
            $productDes = $this->getOne($condition,'*');
            if ($productDes === false) {
                throw new Exception("查询失败");
            }
            return $productDes;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }
}