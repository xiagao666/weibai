<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 19:41
 */
class core_db_Product extends core_db_DbBase {

    private $table = "vb_product";

    /**
     * core_db_news constructor.
     */
    public function __construct()
    {
        parent::__construct($this->table);
    }

    public function addProduct($data) {
        try{
            if(empty($data)) {
                throw new Exception("缺少必要参数");
            }
            $this->useConfig("common","main");
            $rs = $this->insert($this->table,$data);
            if($rs === false) {
                throw new Exception("添加记录失败");
            }
        } catch (Exception $e) {
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
    public function queryProductList($query, $page, $limit, $orderBy){
        try{
            $this->useConfig("common","query");
            $products = $this->getAllData($query, "*", "", $orderBy, "", $limit, $page);
            $productList['total'] = $products->totalSize;
            $productList['items'] = $products->items;
            return $productList;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    public function updateOneProduct($condition, $item){
        try{
            if(empty($condition) || empty($item)) {
                throw new Exception("缺少必要参数");
            }
            $this->useConfig("common","main");
            $id = $condition['id'];
            $itemRes = $this->getOne(array("id"=>$id));
            if(empty($itemRes)) {
                throw new Exception("记录不存在");
            }
            $rs = $this->update($this->table, $condition, $item);
            if($rs === false) {
                throw new Exception("更新失败");
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function getOneProductById($id) {
        try{
            if(empty($id)) {
                throw new Exception("缺少必要参数");
            }
            $this->useConfig("common","main");
            return $this->getOne(array("id"=>$id));
        } catch (Exception $e) {
            return false;
        }
    }

}