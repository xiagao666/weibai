<?php

class index_main extends STpl
{
    function __construct()
    {
    }

    function __destruct()
    {
    }

    /**
     * 首页
     */
    public function pageIndex($inPath)
    {
        /*$condition["type"] = 6;
        $dbCms = new core_db_Cms();
        $images = $dbCms->queryNews($condition, 1, 3);
        $condition["type"] = 1;
        $news = $dbCms->queryNews($condition,1,3," create_date desc ");
        $params["indexImages"] = $images;
        $params["indexNews"] = $news;*/
        return $this->render("index/index.html", $params);
    }

    /**
     * 代理品牌
     */
    public function pageBrand($inPath) {
        $condition["type"] = 2;
        $dbCms = new core_db_Cms();
        $rs = $dbCms->queryNews($condition);
        $params["brands"] = $rs;
        return $this->render("brand/index.html", $params);
    }

    /**
     * 技术服务
     */
    public function pageTech($inPath) {
        $condition["type"] = 3;
        $dbCms = new core_db_Cms();
        $rs = $dbCms->queryNews($condition);
        $params["techs"] = $rs;
        return $this->render("tech/index.html", $params);
    }
    /**
     * 关于唯佰
     */
    public function pageAbout($inPath) {
        $condition["type"] = 4;
        $dbCms = new core_db_Cms();
        $rs = $dbCms->queryNews($condition);
        $params["about"] = $rs;
        return $this->render("about/index.html", $params);
    }
    /**
     * 联系我们
     */
    public function pageContact($inPath) {
        $condition["type"] = 5;
        $dbCms = new core_db_Cms();
        $rs = $dbCms->queryNews($condition);
        $params["contact"] = $rs;
        return $this->render("contact/index.html", $params);
    }

}
