<?php

/**
 * 产品相关
 */
class core_db_Product extends core_db_DbBase
{

    private $table = "vb_product";

    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * 添加产品
     * @param $data
     * @return bool
     */
    public function addProduct($data)
    {
        try {
            core_lib_Comm::p($data);
            if (empty($data)) {
                throw new Exception("缺少必要参数");
            }
            //重要字段判断 产品和包装
            var_dump($data['catalog_number']);
            var_dump($data['package']);
            if (!$data['catalog_number'] || !$data['package']) {
                throw new Exception("缺少导入的重要参数");
            }

            $this->useConfig("common", "main");
            $rs = $this->insertData($data);
            if ($rs === false) {
                throw new Exception("添加记录失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 查询产品列表
     * @param $query
     * @param $page
     * @param $limit
     * @param $orderBy
     * @return bool
     */
    public function queryProductList($query, $orderBy = array("id"=>"desc"), $limit = 10, $page = 0)
    {
        try {
            $this->useConfig("common", "query");
            $products = $this->getAllData($query, "*", "", $orderBy, "", $limit, $page);
            if ($products === false) {
                throw new Exception("查询失败");
            }
            $productList['total'] = $products->totalSize;
            $productList['list'] = $products->items;
            return $productList;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     *  产品ID 产品更新
     * @param $data
     * @return bool
     */
    public function updateProductById($data)
    {
        try {
            core_lib_Comm::p($data);
            $id = $data['id'];
            unset($data['id']);
            if (empty($data) || !$id) {
                throw new Exception("缺少必要参数");
            }

            $product = $this->getProductById($id);
            if ($product == false) {
                throw new Exception("记录不存在");
            }
            $this->useConfig("common", "main");
            $rs = $this->updateData(array('id'=>$id), $data);
            if ($rs === false) {
                throw new Exception("更新失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 产品ID 获取单个产品
     * @param $id
     * @return bool
     */
    public function getProductById($id)
    {
        try {
            if (empty($id)) {
                throw new Exception("缺少必要参数");
            }
            $this->useConfig("common", "main");
            $product = $this->getOne(array("id" => $id));
            if ($product === false) {
                throw new Exception("获取当个产品失败");
            }
            return $product;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }
}