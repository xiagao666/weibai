<?php

/**
 * 浏览历史
 * db_viewHistory
 */
class core_db_ViewHistory extends core_db_DbBase
{
    private $table = "vb_view_history";

    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * 添加浏览记录
     * @param $data
     * @return bool
     */
    public function addViewHistory($data)
    {
        try {
            if (empty($data)) {
                throw new Exception("缺少必要参数");
            }

            $query['uuid'] = $data['uuid'];
            $query['product_id'] = $data['product_id'];
            $views = $this->getViewHistorys($query, 1, 1);
            if (time() - strtotime($views['data'][0]['create_time']) <= 30 * 60) {
                throw new Exception("30分钟内浏览过");
            }

            //判断数据必选项
            $this->useConfig("common", "main");
            $data['create_time'] = date('Y-m-d H:i:s', time());
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
     * 获取浏览记录
     */
    public function getViewHistorys($query, $limit = 3, $page = 0)
    {
        try {
            //判断数据必选项
            $this->useConfig("common", "query");
            $views = $this->getAllData($query, "*", "", array('create_time'=>'desc'), "", $limit, $page);
            if ($views === false) {
                throw new Exception("读取失败");
            }
            $viewHistorys['total'] = $views->totalSize;
            $viewHistorys['data'] = $views->items;
            return $viewHistorys;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 清空浏览记录
     */
    public function delViewHistorys($uuid) {
        try {
            if (!$uuid) {
                throw new Exception("缺少参数");
            }
            //判断数据必选项
            $this->useConfig("common", "main");
            $history = $this->deleteData(array('uuid'=>$uuid));
            return $history;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }
}
