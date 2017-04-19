<?php

/**
 * DB_Base
 **/
class core_db_DbBase extends SDb
{
    protected $_time;
    protected $_ymd;
    protected $_error;//错误信息
    protected $_tableName;

    function __construct($tableName = "", $config = array())
    {
        parent::__construct($tableName, $config);
        $this->_time = time();
        $this->_ymd = date('Y-m-d', $this->_time);
        $this->_tableName = $tableName;
    }

    /**
     * 写入错误信息
     * @param int $code
     * @param string $msg
     */
    protected function setError($msg = "", $code = 0)
    {
        $this->_error["code"] = $code;
        $this->_error["msg"] = $msg;
    }

    /**
     * 获取错误信息
     * @param string $type
     */
    public function getError($type = "msg")
    {
        return $this->_error[$type];
    }

    /**
     * 异常日志记录
     */
    public function log($e)
    {
        $errorMsg['message'] = $e->getMessage();
        $errorMsg['file'] = $e->getFile();
        $errorMsg['code'] = $e->getCode();
        $errorMsg['line'] = $e->getLine();
        $this->setError($e->getMessage(), $e->getCode());
        error_log(json_encode($errorMsg));
    }

    /**
     * 查询单个数据
     */
    public function getOne($condition = "", $item = "", $groupby = "", $orderby = "", $leftjoin = "")
    {
        $this->setLimit(1);
        $this->setCount(false);
        $data = $this->select($this->_tableName, $condition, $item, $groupby, $orderby, $leftjoin);
        if (isset($data->items[0])) {
            return $data->items[0];
        } else {
            return false;
        }
    }

    /**
     * 查询多个数据
     */
    public function getAllData($condition = "", $item = "", $groupby = "", $orderby = "", $leftjoin = "", $limit = 10, $page = 0)
    {
        $this->setLimit($limit);
        $this->setPage($page);
        $this->setCount(true);
        $data = $this->select($this->_tableName, $condition, $item, $groupby, $orderby, $leftjoin);
        if ($data->totalSize > 0) {
            return $data;
        } else {
            return false;
        }
    }

    /**
     * 插入数据
     */
    public function insertData($item = "", $isreplace = false, $isdelayed = false, $update = array())
    {
        return parent::insert($this->_tableName, $item, $isreplace, $isdelayed, $update);
    }

    /**
     * 更改数据
     */
    public function updateData($condition, $item)
    {
        return parent::update($this->_tableName, $condition, $item);
    }

    /**
     * 删除
     */
    public function deleteData($condition)
    {
        return parent::delete($this->_tableName, $condition);
    }
}