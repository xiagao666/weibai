<?php

/**
 * db_category
 **/
class core_db_Category extends core_db_DbBase
{
    private $table = "vb_category";

    public function __construct()
    {
        parent::__construct($this->table);
    }

    public function addCategory($data)
    {
        try {
            //判断数据必选项
            $this->useConfig("common", "main");
            $rs = $this->insert($this->table, $data);
            if ($rs === false) {
                throw new Exception("数据写入失败");
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function updateOneCategory($condition, $item){
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
    public function queryAllCategory($condition) {
        try {
            $this->useConfig("common", "query");
            return $this->select($this->table, $condition);
        } catch (Exception $e) {
            return false;
        }
    }
}
