<?php

class index_main extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    function __destruct()
    {
    }

    /**
     * 首页
     */
    public function pageIndex($inPath)
    {
        $this->getNewest();//最新新闻
        /*$condition["type"] = 6;
        $dbCms = new core_db_Cms();
        $images = $dbCms->queryNews($condition, 1, 3);
        $condition["type"] = 1;
        $news = $dbCms->queryNews($condition,1,3," create_date desc ");
        $params["indexImages"] = $images;
        $params["indexNews"] = $news;*/
        return $this->render("index/index.html", $this->_params);
    }

    /**
     * 代理品牌
     */
    public function pageBrand($inPath)
    {
        $query["type"] = 2;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $dbCms = new core_db_Cms();
        $brands = $dbCms->queryNews($query, 16, 1);

        $this->_params['brands'] =$brands['data'];
        $this->_params['currNav'] = "brand";
        return $this->render("brand/index.html", $this->_params);
    }

    /**
     * 技术服务
     */
    public function pageTech($inPath)
    {
        $query["type"] = 2;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $dbCms = new core_db_Cms();
        $brands = $dbCms->queryNews($query, 16, 1);

        $this->_params['brands'] =$brands['data'];
        $this->_params['currNav'] = "brand";
        return $this->render("tech/index.html", $this->_params);
    }

    /**
     * 关于唯佰
     */
    public function pageAbout($inPath)
    {
        $condition["type"] = 4;
        $dbCms = new core_db_Cms();
        $rs = $dbCms->queryNews($condition);
        $params["about"] = $rs;
        return $this->render("about/index.html", $params);
    }

    /**
     * 联系我们
     */
    public function pageContact($inPath)
    {
        $condition["type"] = 5;
        $dbCms = new core_db_Cms();
        $rs = $dbCms->queryNews($condition);
        $params["contact"] = $rs;
        return $this->render("contact/index.html", $params);
    }

    /**
     * 获取首页的新闻
     */
    public function getNewest()
    {
        $query['type'] = 1;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $newsList = $this->_dbCms->queryNews($query, 3, 1);
        if ($newsList['data']) {
            foreach ($newsList['data'] as $nek => $nev) {
                $newsList['data'][$nek]['createYmd'] = date("[m/d] Y", strtotime($nev['create_date']));
            }
        }

        $this->_params['newsList'] = $newsList['data'];
    }

}
