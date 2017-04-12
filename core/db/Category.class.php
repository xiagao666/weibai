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

            $category = $this->getCategoryByName($data['name']);
            if ($category !== false) {
                throw new Exception("不能添加重复的分类");
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
     * 通过ID获取单个分类
     */
    public function getCategoryById($categoryId)
    {
        try {
            if (!$categoryId) {
                throw new Exception("缺少必要参数");
            }
            //判断数据必选项
            $this->useConfig("common", "query");
            $category = $this->getOne(array('id'=>$categoryId), '*');
            if ($category === false) {
                throw new Exception("读取失败");
            }
            return $category;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 通过分类名称获取单个分类
     */
    public function getCategoryByName($categoryName)
    {
        try {
            if (!$categoryName) {
                throw new Exception("缺少必要参数");
            }
            //判断数据必选项
            $this->useConfig("common", "query");
            $category = $this->getOne(array('name'=>$categoryName), '*');
            if ($category === false) {
                throw new Exception("读取失败");
            }
            return $category;
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
    public function updateCategoryById($data)
    {
        try {
            $categoryId = $data['id'];
            unset($data['id']);
            if (!$categoryId || empty($data)) {
                throw new Exception("缺少必要参数");
            }
            $category = $this->getCategoryById($categoryId);
            if ($category === false) {
                throw new Exception("编辑的分类不存在");
            }

            $this->useConfig("common", "main");
            $rs = $this->updateData(array("id"=>$categoryId), $data);
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
     * 查询分类
     * @param $condition 查询条件
     * @return bool
     */
    public function queryAllCategory($condition = array())
    {
        try {
            $this->useConfig("common", "query");
            $categorys = $this->getAllData($condition, "", "", array("show_sort"=>"desc", "id"=>"desc"));
            if ($categorys === false) {
                throw new Exception("查询分类失败");
            }
            $categoryList['total'] = $categorys->totalSize;
            $categoryList['items'] = $categorys->items;
            return $categoryList;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }
}
