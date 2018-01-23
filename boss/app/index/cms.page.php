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
        $searchKey = isset($_GET['searchKey']) ? trim(core_lib_Comm::getStr($_GET['searchKey'])) : '';
        $searchValue = isset($_GET['searchVal']) ? trim(core_lib_Comm::getStr($_GET['searchVal'])) : '';
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
     * 活动管理
     */
    public function pageActivity($inPath)
    {
        $searchKey = isset($_GET['searchKey']) ? trim(core_lib_Comm::getStr($_GET['searchKey'])) : '';
        $searchValue = isset($_GET['searchVal']) ? trim(core_lib_Comm::getStr($_GET['searchVal'])) : '';
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 10;

        $dbCms = new core_db_Cms();

        $query['type'] = 11;
        if ($searchKey && $searchValue) {
            $query[] = " `{$searchKey}` like '%{$searchValue}%' ";
        }
        $newsList = $dbCms->queryNews($query, $limit, $page);

        $this->pageBar($newsList['total'], $limit, $page, '/cms/news');

        $this->_params['searchKey'] = $searchKey;
        $this->_params['searchVal'] = $searchValue;
        $this->_params['cmsData'] = $newsList['data'];
        $this->_params['url'] = WWW_URL."/activity/detail?id=";
        return $this->render("activity/list.html", $this->_params);
    }

    /**
     * 品牌代理
     */
    public function pageBrand($inPath)
    {
        $searchKey = isset($_GET['searchKey']) ? trim(core_lib_Comm::getStr($_GET['searchKey'])) : '';
        $searchValue = isset($_GET['searchVal']) ? trim(core_lib_Comm::getStr($_GET['searchVal'])) : '';
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 10;

        $dbCms = new core_db_Cms();

        $query['type'] = 2;//代理品牌
        if ($searchKey && $searchValue) {
            $query[] = " `{$searchKey}` like '%{$searchValue}%' ";
        }
        $brandList = $dbCms->queryNews($query, $limit, $page);

        $this->pageBar($brandList['total'], $limit, $page, '/cms/brand');

        $this->_params['searchKey'] = $searchKey;
        $this->_params['searchVal'] = $searchValue;
        $this->_params['cmsData'] = $brandList['data'];
        return $this->render("brand/list.html", $this->_params);
    }

    /**
     * 技术服务
     */
    public function pageTech($inPath)
    {
        $searchKey = isset($_GET['searchKey']) ? trim(core_lib_Comm::getStr($_GET['searchKey'])) : '';
        $searchValue = isset($_GET['searchVal']) ? trim(core_lib_Comm::getStr($_GET['searchVal'])) : '';
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 10;

        $dbCms = new core_db_Cms();

        $query['type'] = 3;//技术服务
        if ($searchKey && $searchValue) {
            $query[] = " `{$searchKey}` like '%{$searchValue}%' ";
        }
        $techList = $dbCms->queryNews($query, $limit, $page);

        $this->pageBar($techList['total'], $limit, $page, '/cms/tech');

        $this->_params['searchKey'] = $searchKey;
        $this->_params['searchVal'] = $searchValue;
        $this->_params['cmsData'] = $techList['data'];
        return $this->render("tech/list.html", $this->_params);
    }

    /**
     * 关于韵飞
     */
    public function pageAbout($inPath)
    {
        $dbCms = new core_db_Cms();

        $query['type'] = 4;//关于韵飞
        $about = $dbCms->queryNews($query, 1, 1);
        if (is_array($about['data'][0])) {
            $isEdit = 1;
        }
        $this->_params['about'] = $about['data'][0];
        $this->_params['type'] = 4;
        $this->_params['isEdit'] = $isEdit;
        return $this->render("about/action.html", $this->_params);
    }

    /**
     * 联系我们
     */
    public function pageContact($inPath)
    {
        $dbCms = new core_db_Cms();

        $query['type'] = 5;//联系我们
        $about = $dbCms->queryNews($query, 1, 1);
        if (is_array($about['data'][0])) {
            $isEdit = 1;
        }
        $this->_params['contact'] = $about['data'][0];
        $this->_params['type'] = 5;
        $this->_params['isEdit'] = $isEdit;
        return $this->render("contact/action.html", $this->_params);
    }

    /**
     * 针对于通用的cms 比如首页滚动图
     * cms管理相关
     */
    public function pageOther($inPath)
    {
        $type = isset($_GET['type']) ? core_lib_Comm::getStr($_GET["type"], 'int') : 0;
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET["limit"], 'int') : 10;

        $dbCms = new core_db_Cms();
        $typeNames = $dbCms->getTypeNameByType();
        unset($typeNames[0]);
        unset($typeNames[1]);
        unset($typeNames[2]);
        unset($typeNames[3]);
        unset($typeNames[4]);
        unset($typeNames[5]);

        if ($type) {
            $query['type'] = $type;
        } else {
            $query[] = "type in (6,7,8,9,10)";
        }
        $techList = $dbCms->queryNews($query, $limit, $page);

        $this->pageBar($techList['total'], $limit, $page, '/cms/other');
        $this->_params['type'] = $type;
        $this->_params['types'] = $typeNames;
        $this->_params['cmsData'] = $techList['data'];
        return $this->render("cms/list.html", $this->_params);
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
        $typeNames = $dbCms->getTypeNameByType();
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

            if (!$type) {
                return $this->alert(array("status"=>"error", "msg"=>"缺少类型"));
            }
            if (!$title) {
                return $this->alert(array("status"=>"error", "msg"=>"缺少标题"));
            }

            $typeName = $typeNames[$type];
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
            case 2://代理品牌
                $tpl = "brand/action.html";
                break;
            case 3://技术服务
                $tpl = "tech/action.html";
                break;
            case 4://关于我们
                $tpl = "about/action.html";
                break;
            case 11://活动
                $tpl = "activity/action.html";
                break;
            default:
                $tpl = "cms/action.html";
        }

        if (!$type) {
            unset($typeNames[0]);
            unset($typeNames[1]);
            unset($typeNames[2]);
            unset($typeNames[3]);
            unset($typeNames[4]);
            unset($typeNames[5]);
        }

        $this->_params['type'] = $type;
        $this->_params['types'] = $typeNames;
        $this->_params['cmsId'] = $cmsId;
        $this->_params['isEdit'] = $isEdit;
        return $this->render($tpl, $this->_params);
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
        if ($rs === false) {
            return $this->alert(array("status"=>"error","msg"=>"删除{$typeName}失败！"));
        }
        return $this->alert(array("status"=>"success","msg"=>"删除{$typeName}成功"));
    }
}
