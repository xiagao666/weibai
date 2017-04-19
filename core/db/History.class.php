<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 19:41
 */
class core_db_History extends core_db_DbBase {

    private $table = "vb_view_history";

    /**
     * core_db_news constructor.
     */
    public function __construct()
    {
        parent::__construct($this->table);
    }

    public function addViewHistory($data) {
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

    public function queryHistoryList($param, $page, $limit){
        try{
            $this->useConfig("common","query");
            $this->setPage((int)$page);
            $this->setLimit((int)$limit);
            return $this->select($this->table,$param,'*');
        } catch (Exception $e) {
            return false;
        }
    }

}