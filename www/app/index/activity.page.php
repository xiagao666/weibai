<?php

/**
 * 活动
 */
class index_activity extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 新闻详情页
     */
    public function pageDetail()
    {
        $id = isset($_GET['id']) ? core_lib_Comm::getStr($_GET["id"], 'int') : 0;
        if (!$id) {
            return $this->alert(array("status"=>"error", "msg"=>"缺少必要参数"));
        }
        $newsInfo = $this->_dbCms->getCmsById($id);
        $this->_params['news'] = $newsInfo;
        return $this->render("activity/detail.html", $this->_params);
    }
}
