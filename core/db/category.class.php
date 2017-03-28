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

    public function add($data)
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

    public function getOne($id)
    {

    }

    public function getAll()
    {

    }

    /*private $_db;

    function __construct($zone = "index")
    {
        $this->_db = new SDb;
        $this->_db->useConfig($zone, "main");
    }

    function add()
    {
        return $this->_db->insert($table = "test",
            $items = array("name" => "testName", "password" => "testPassword" . date("Y-m-d H:i:s")));
    }

    function get($id)
    {
        return $this->_db->selectOne($table = "test", $condition = array("id" => $id),
            $items = array("id", "name", "password"));
    }

    function getAll()
    {
        return $this->_db->select($table = "test", $condition = array(), $items = array("id", "name", "password"));
    }*/
}
