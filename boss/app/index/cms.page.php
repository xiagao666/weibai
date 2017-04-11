<?php

class index_main extends index_base
{
    function __construct()
    {
    }

    function __destruct()
    {
    }

    /**
 * 公司新闻
 */
    public function pageNews($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 1;
        $rs = $dbCms->queryNews($condition,1,20);
        $param["rs"] = $rs;
        return $this->render("boss/news.html",$param);
    }
    /**
     * 品牌代理
     */
    public function pageBrand($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 2;
        $rs = $dbCms->queryNews($condition,1,20);
        $param["rs"] = $rs;
        return $this->render("boss/brand.html",$param);
    }
    /**
    * 技术服务
    */
    public function pageTech($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 3;
        $rs = $dbCms->queryNews($condition,1,20);
        $param["rs"] = $rs;
        return $this->render("boss/tech.html",$param);
    }
    /**
    * 关于唯佰
    */
    public function pageAbout($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 4;
        $rs = $dbCms->queryNews($condition,1,20);
        $param["rs"] = $rs;
        return $this->render("boss/about.html",$param);
    }

    /**
     * 查询
     */
    public function pageQueryList($inPath) {
        $dbCms = new core_db_Cms();
        $condition["type"] = $_GET["type"];
        $page = $_GET["page"];
        $size = $_GET["size"];
        $rs = $dbCms->queryNews($condition,$page,$size);
        echo json_encode($rs);
    }
}
