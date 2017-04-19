<?php

class index_cms extends index_base
{
    public function __construct()
    {
//        parent::__construct();
    }

    /**
     * 公司新闻
     */
    public function pageNews($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition = " type=1 ";
        $searchKey = $_GET['searchKey'];
        $searchValue = $_GET['searchVal'];
        if(!empty($searchKey) && !empty($searchValue)) {
            $condition = $condition." and ".$searchKey." like '%".$searchValue."%'";
        }
        $rs = $dbCms->queryNews($condition, 1, 20, "");
        $param["cmsData"] = $rs->items;
        $param['searchKey'] = $searchKey;
        $param['searchVal'] = $searchValue;
        return $this->render("/news/list.html", $param);
    }

    /**
     * 品牌代理
     */
    public function pageBrand($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition = " type=2 ";
        $searchKey = $_GET['searchKey'];
        $searchValue = $_GET['searchVal'];
        if(!empty($searchKey) && !empty($searchValue)) {
            $condition = $condition." and ".$searchKey." like '%".$searchValue."%'";
        }
        $rs = $dbCms->queryNews($condition, 1, 20, "");
        $param["cmsData"] = $rs->items;
        $param['searchKey'] = $searchKey;
        $param['searchVal'] = $searchValue;
        return $this->render("/brand/list.html", $param);
    }

    /**
     * 技术服务
     */
    public function pageTech($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition = " type=3 ";
        $searchKey = $_GET['searchKey'];
        $searchValue = $_GET['searchVal'];
        if(!empty($searchKey) && !empty($searchValue)) {
            $condition = $condition." and ".$searchKey." like '%".$searchValue."%'";
        }
        $rs = $dbCms->queryNews($condition, 1, 20, "");
        $param["cmsData"] = $rs->items;
        $param['searchKey'] = $searchKey;
        $param['searchVal'] = $searchValue;
        return $this->render("/tech/list.html", $param);
    }

    /**
     * 关于唯佰
     */
    public function pageAbout($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = 4;
        $rs = $dbCms->getOneCms($condition);
        $param["cmsData"] = $rs;
        return $this->render("/about/list.html", $param);
    }

    /**
     * 查询
     */
    public function pageQueryList($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["type"] = $_GET["type"];
        $page = $_GET["page"];
        $size = $_GET["size"];
        $rs = $dbCms->queryNews($condition, $page, $size, "");
        echo json_encode($rs);
    }

    /**
     * 根据ID获取信息
     */
    public function pageGetOneById($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["id"] = $_GET["cmsId"];
        $rs = $dbCms->getOneCms($condition);
        echo json_encode($rs);
    }

    /**
     * 更新cms信息
     */
    public function pageUpdate($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["id"] = $_REQUEST["cmsId"];
        $item["title"] = isset($_REQUEST['title']) ? core_lib_Comm::getStr($_REQUEST['title']) : '';//ID;
        $item["des"] = isset($_REQUEST['des']) ? core_lib_Comm::getStr($_REQUEST['des']) : '';
        //$item["img_url"] = $_REQUEST["imgUrl"];
        //$item["content"] = $_REQUEST["content"];
        $item["hyperlink"] = isset($_REQUEST['hyperlink']) ? core_lib_Comm::getStr($_REQUEST['hyperlink']) : '';
        $item["last_update_date"] = date("y-m-d H:i:s", time());
        $item["content"] = $_REQUEST['content'];
        $rs = $dbCms->updateOneNews($condition, $item);
        if ($rs) {
            $params = array("status"=>"success","msg"=>"更新新闻成功！");
        } else {
            $params = array("status"=>"error","msg"=>"更新新闻失败，请稍后重试！");
        }
        return $this->alert($params);
    }

    /**
     * 删除
     */
    public function pageDelete($inPath)
    {
        $dbCms = new core_db_Cms();
        $condition["id"] = $_GET["cmsId"];
        $rs = $dbCms->deleteOneCms($condition);
        if ($rs) {
            $params = array("status"=>"success","msg"=>"删除新闻成功！");
        } else {
            $params = array("status"=>"error","msg"=>"删除新闻失败！");
        }
        return $this->alert($params);
    }

    public function pageAdd($inPath)
    {
        $dbCms = new core_db_Cms();
        $data["title"] = $_REQUEST["title"];
        $data["des"] = $_REQUEST["des"];
        $data["hyperlink"] = $_REQUEST["hyperlink"];
        $data["type"] = $_REQUEST["cmsType"];
        $data["create_date"] = date("y-m-d H:i:s", time());
        $data["last_update_date"] = date("y-m-d H:i:s", time());
        $data['content'] = $_REQUEST['content'];
        $rs = $dbCms->addNews($data);
        if ($rs) {
            $params = array("status"=>"success","msg"=>"添加新闻成功！");
        } else {
            $params = array("status"=>"error","msg"=>"添加新闻失败，请稍后重试！");
        }
        return $this->alert($params);
    }
}
