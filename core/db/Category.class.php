<?php

/**
 * 类别相关
 * db_Category
 */
class core_db_Category extends core_db_DbBase
{
    private $table = "vb_category";

    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * 添加分类
     * @param $data
     * @return bool
     */
    public function addCategory($data)
    {
        try {
            if (empty($data)) {
                throw new Exception("缺少必要参数");
            }
            //判断数据必选项
            $this->useConfig("common", "main");
            $rs = $this->insertData($data);
            if ($rs === false) {
                throw new Exception("数据写入失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 更新分类
     * @param $data 查询条件
     * @param $item 字段
     * @return $rs
     */
    public function updateOneCategory($condition, $item)
    {
        try {
            if (empty($condition) || empty($item)) {
                throw new Exception("缺少必要参数");
            }
            $this->useConfig("common", "main");
            $id = $condition['id'];
            $itemRes = $this->getOne(array("id" => $id));
            if (empty($itemRes)) {
                throw new Exception("记录不存在");
            }
            $rs = $this->updateData($condition, $item);
            if ($rs === false) {
                throw new Exception("更新失败");
            }
            return $rs;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 查询分类
     * @param $condition 查询条件
     * @return bool
     */
    public function queryAllCategory($condition)
    {
        try {
            $this->useConfig("common", "query");
            $categorys = $this->getAllData($condition);
            return $categorys;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }
}
