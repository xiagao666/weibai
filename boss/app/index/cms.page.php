<?php

class index_cms extends index_base
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
        $rs = $dbCms->queryNews($condition,1,20,"");
        $param["cmsData"] = $rs->items;
        return $this->render("boss/news.html",$param);
    }
    /**
     * 品牌代理
     */
    public function pageBrand($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 2;
        $rs = $dbCms->queryNews($condition,1,20,"");
        $param["cmsData"] = $rs->items;
        return $this->render("boss/brand.html",$param);
    }
    /**
    * 技术服务
    */
    public function pageTech($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 3;
        $rs = $dbCms->queryNews($condition,1,20,"");
        $param["cmsData"] = $rs->items;
        return $this->render("boss/tech.html",$param);
    }
    /**
    * 关于唯佰
    */
    public function pageAbout($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 4;
        $rs = $dbCms->queryNews($condition,1,20,"");
        $param["cmsData"] = $rs->items;
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
        $rs = $dbCms->queryNews($condition,$page,$size,"");
        echo json_encode($rs);
    }

    /**
     * 根据ID获取信息
     */
    public function pageGetOneById($inPath) {
        $dbCms = new core_db_Cms();
        $condition["id"] = $_GET["cmsId"];
        $rs = $dbCms->getOneCms($condition);
        echo json_encode($rs);
    }

    /**
     * 更新cms信息
     */
    public function pageUpdate($inPath) {
        $dbCms = new core_db_Cms();
        $condition["id"] = $_GET["cmsId"];
        $item["title"] = $_GET["title"];
        $item["des"] = $_GET["des"];
        //$item["img_url"] = $_GET["imgUrl"];
        //$item["content"] = $_GET["content"];
        $item["hyperlink"] = $_GET["hyperlink"];
        $item["last_update_date"] = date("y-m-d H:i:s",time());
        $item["content"] = $_GET["content"];
        $rs = $dbCms->updateOneNews($condition, $item);
        if($rs){
            $params["success"] = true;
        }else{
            $params["success"] = false;
        }
        echo json_encode($params);
    }

    /**
     * 删除
     */
    public function pageDelete($inPath){
        $dbCms = new core_db_Cms();
        $condition["id"] = $_GET["cmsId"];
        $rs = $dbCms->deleteOneCms($condition);
        if($rs){
            $params["success"] = true;
        }else{
            $params["success"] = false;
        }
        echo json_encode($params);
    }

    public function pageAdd($inPath) {
        $dbCms = new core_db_Cms();
        $data["title"] = $_GET["title"];
        $data["des"] = $_GET["des"];
        $data["hyperlink"] = $_GET["hyperlink"];
        $data["type"] = $_GET["cmsType"];
        $data["create_date"] = date("y-m-d H:i:s",time());
        $data["last_update_date"] = date("y-m-d H:i:s",time());
        $rs = $dbCms->addNews($data);
        if($rs){
            $params["success"] = true;
        }else{
            $params["success"] = true;
        }
        return json_encode($params);
    }
}
