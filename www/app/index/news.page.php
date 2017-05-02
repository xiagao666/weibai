<?php

/**
 * 新闻相关
 */
class index_news extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 新闻列表
     */
    public function pageIndex()
    {
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = 10;

        $query['type'] = 1;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $news = $this->_dbCms->queryNews($query, 1, 1);

        $query['type'] = 1;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $query[] = " id != ".$news['data'][0]['id'];
        $newsList = $this->_dbCms->queryNews($query, $limit, $page);
        $totalPage = ceil($newsList['total'] / $limit);

        if ($newsList['data']) {
            foreach ($newsList['data'] as $nek => $nev) {
                $newsList['data'][$nek]['createYmd'] = date("Y-m-d", strtotime($nev['create_date']));
            }
        }

        $this->_params['newsList'] = $newsList['data'];
        $this->_params['firstNews'] = $news['data'][0];
        $this->_params['page'] = $page;
        $this->_params['totalPage'] = $totalPage;
        return $this->render("news/index.html", $this->_params);
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
        core_lib_Comm::p($newsInfo);
        $this->_params['news'] = $newsInfo;
        return $this->render("news/detail.html", $this->_params);
    }
}
