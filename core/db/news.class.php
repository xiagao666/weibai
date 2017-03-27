<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 19:41
 */
class core_db_news extends core_db_DbBase {

    private $table = "vb_news";

    /**
     * core_db_news constructor.
     */
    public function __construct()
    {
        parent::__construct($this->table);
    }

    public function addNews($data) {
        try{
            $this->useConfig("common","main");
            $rs = $this->insert($this->table,$data);
            if($rs === false) {
                throw new Exception("添加记录失败");
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public function queryNews($param, $page, $limit){
        try{
            $this->useConfig("comman","query");
            $this->setPage($page);
            $this->setLimit($limit);
            return $this->select($this->table,$param);
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateOneNews($condition, $item){
        try{
            $this->useConfig("comman","main");
            $rs = $this->update($this->table, $condition, $item);
            if($rs === false) {
                throw new Exception("更新失败");
            }
        } catch (Exception $e) {
            return false;
        }
    }

}