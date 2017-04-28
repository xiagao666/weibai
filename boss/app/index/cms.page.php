<?php

class index_cms extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 公司新闻
     */
    public function pageNews($inPath)
    {
        $searchKey = isset($_GET['searchKey']) ? core_lib_Comm::getStr($_GET['searchKey']) : '';
        $searchValue = isset($_GET['searchVal']) ? core_lib_Comm::getStr($_GET['searchVal']) : '';
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 10;

        $dbCms = new core_db_Cms();

        $query['type'] = 1;
        if ($searchKey && $searchValue) {
            $query[] = " `{$searchKey}` like '%{$searchValue}%' ";
        }
        $newsList = $dbCms->queryNews($query, $limit, $page);

        $this->pageBar($newsList['total'], $limit, $page, '/cms/news');

        $this->_params['searchKey'] = $searchKey;
        $this->_params['searchVal'] = $searchValue;
        $this->_params['cmsData'] = $newsList['data'];
        return $this->render("news/list.html", $this->_params);
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
        if (empty($rs)){
            return $this->alert(array('status'=>'error','msg'=>"获取新闻信息失败！"));
        }
        return $this->alert(array('status'=>'success','msg'=>"获取新闻信息成功！", "data"=>$rs));
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
     * 删除内容
     */
    public function pageDelete($inPath)
    {
        $cmsId = isset($_REQUEST['cmsId']) ? core_lib_Comm::getStr($_REQUEST['cmsId'], 'int') : 0;// 内容ID

        if (!$cmsId) {
            return $this->alert(array('status'=>'error','msg'=>"缺少内容ID"));
        }
        $dbCms = new core_db_Cms();
        $cmsInfo = $dbCms->getCmsById($cmsId);
        if ($cmsInfo === false) {
            return $this->alert(array('status'=>'error','msg'=>"删除数据不存在"));
        }
        $typeName = $dbCms->getTypeNameByType($cmsInfo['type']);


        $rs = $dbCms->deleteCmsById($cmsId);
        core_lib_Comm::p($rs);
        if ($rs === false) {
            return $this->alert(array("status"=>"error","msg"=>"删除{$typeName}失败！"));
        }
        return $this->alert(array("status"=>"success","msg"=>"删除{$typeName}成功"));
    }

    /**
     * 添加CMS内容
     * @param $inPath
     * @return string|voi
     */
    public function pageAction($inPath)
    {
        $isEdit = isset($_REQUEST['isEdit']) ? core_lib_Comm::getStr($_REQUEST['isEdit'], 'int') : 0;//是否编辑 1 编辑 0 添加
        $cmsId = isset($_REQUEST['cmsId']) ? core_lib_Comm::getStr($_REQUEST['cmsId'], 'int') : 0;// 内容ID
        $type = isset($_REQUEST['type']) ? core_lib_Comm::getStr($_REQUEST['type'], 'int') : 0;//类型
        $msg = "添加";

        $dbCms = new core_db_Cms();
        if ($isEdit) {
            if (!$cmsId) {
                return $this->alert(array('status'=>'error','msg'=>"缺少内容ID"));
            }
            $cmsInfo = $dbCms->getCmsById($cmsId);
            if ($cmsInfo === false) {
                return $this->alert(array('status'=>'error','msg'=>"编辑内容不存在"));
            }
            $this->_params['cms'] = $cmsInfo;
            $msg = "编辑";
        }

        if ($_POST) {
            $title = isset($_POST['title']) ? core_lib_Comm::getStr($_POST['title']) : 0;//标题
            $des = isset($_POST['des']) ? core_lib_Comm::getStr($_POST['des']) : 0;//描述
            $imgUrl = isset($_POST['imgUrl']) ? core_lib_Comm::getStr($_POST['imgUrl']) : 0;//图片地址
            $content = isset($_POST['content']) ? core_lib_Comm::reMoveXss($_POST['content']) : 0;//内容
            $type = isset($_POST['type']) ? core_lib_Comm::getStr($_POST['type'], 'int') : 0;//类型
            $relationId = isset($_POST['relationId']) ? core_lib_Comm::getStr($_POST['relationId'], 'int') : 0;//关联新闻ID
            $hyperlink = isset($_POST['hyperlink']) ? core_lib_Comm::getStr($_POST['hyperlink']) : 0;//链接
            $sort = isset($_POST['sort']) ? core_lib_Comm::getStr($_POST['sort'], 'int') : 0;//排序

            $typeName = $dbCms->getTypeNameByType($type);
            $data["title"] = $title;
            $data["des"] = $des;
            $data["img_url"] = $imgUrl;
            $data["content"] = $content;
            $data["type"] = $type;
            $data["relation_id"] = $relationId;
            $data["hyperlink"] = $hyperlink;
            $data["sort"] = $sort;
            $data["create_date"] = date("y-m-d H:i:s", time());
            if ($isEdit) {
                $data['id'] = $cmsId;
                $rs = $dbCms->updateCmsById($data);
            } else {
                $rs = $dbCms->addNews($data);
            }

            if ($rs === false) {
                return $this->alert(array("status"=>"error", "msg"=>$msg.$typeName."失败"));
            }
            return $this->alert(array("status"=>"success", "msg"=>$msg.$typeName."成功"));
        }

        switch ($type) {
            case 1://新闻
                $tpl = "news/action.html";
                break;

        }

        $this->_params['type'] = $type;
        $this->_params['cmsId'] = $cmsId;
        $this->_params['isEdit'] = $isEdit;
        return $this->render($tpl, $this->_params);
    }



    /**
     * 针对于通用的cms 比如首页滚动图
     */
    public function pageOther($inPath){
        $dbCms = new core_db_Cms();
        if($_GET['cmsType'] == 0){
            $condition = "type in (6)";
        }else{
            $condition['type'] = $_GET['cmsType'];
        }
        $rs = $dbCms->queryNews($condition, 1, 20, "");
        $param["cmsData"] = $rs->items;
        $param['cmsType'] = $_GET['cmsType'];
        return $this->render("/cms/list.html", $param);
    }
}
