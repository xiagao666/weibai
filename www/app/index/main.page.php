<?php

class index_main extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 首页
     */
    public function pageIndex($inPath)
    {
        $this->getNewest();//最新新闻
        $this->getSaleImg();//产品促销大图
        $this->getSaleProduct();//促销产品
        $this->getNewsLeftImg();//新闻资讯左图
        return $this->render("index/index.html", $this->_params);
    }

    /**
     * 代理品牌
     */
    public function pageBrand($inPath)
    {
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = 16;
        $query["type"] = 2;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $dbCms = new core_db_Cms();
        $brands = $dbCms->queryNews($query, $limit, $page);
        $totalPage = ceil($brands['total'] / $limit);

        $this->_params['brands'] = $brands['data'];
        $this->_params['totalPage'] = $totalPage;
        $this->_params['page'] = $page;
        $this->_params['currNav'] = "/main/brand";
        return $this->render("brand/index.html", $this->_params);
    }

    /**
     * 技术服务
     */
    public function pageTech($inPath)
    {
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET["page"], 'int') : 1;
        $limit = 16;

        $query["type"] = 3;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $dbCms = new core_db_Cms();
        $topTech = $dbCms->queryNews($query, 1, 1);
        $bigTech = $topTech['data'][0];

        $query["type"] = 3;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $query[] = " id != ".$bigTech['id'];
        $dbCms = new core_db_Cms();
        $techs = $dbCms->queryNews($query, $limit, $page);
        $totalPage = ceil($techs['total'] /  $limit);

        $this->_params['techs'] = $techs['data'];
        $this->_params['bigTech'] = $bigTech;
        $this->_params['totalPage'] = $totalPage;
        $this->_params['page'] = $page;
        $this->_params['currNav'] = "/main/tech";
        return $this->render("tech/index.html", $this->_params);
    }

    /**
     * 关于唯佰
     */
    public function pageAbout($inPath)
    {
        $query["type"] = 4;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $dbCms = new core_db_Cms();
        $abouts = $dbCms->queryNews($query, 1, 1);

        $about = $abouts['data'][0];

        $this->_params['about'] = $about;
        $this->_params['currNav'] = "/main/about";
        return $this->render("about/index.html", $this->_params);
    }

    /**
     * 联系我们
     */
    public function pageContact($inPath)
    {
        $query["type"] = 5;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $dbCms = new core_db_Cms();
        $contacts = $dbCms->queryNews($query, 1, 1);

        $contact = $contacts['data'][0];

        $this->_params['contact'] = $contact;
        $this->_params['currNav'] = "/main/contact";
        return $this->render("contact/index.html", $this->_params);
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

    /**
     * 首页-产品促销-大图
     */
    public function getSaleImg()
    {
        $query['type'] = 9;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $sales = $this->_dbCms->queryNews($query, 3, 1);
        $this->_params['saleImg'] = $sales['data'];
    }

    /**
     * 首页-新闻资讯-左图
     */
    public function getNewsLeftImg()
    {
        $query['type'] = 10;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $leftImgs = $this->_dbCms->queryNews($query, 3, 1);
        $this->_params['leftImg'] = $leftImgs['data'];
    }

    /**
     * 获取促销商品
     */
    public function getSaleProduct()
    {
        $query['is_sale'] = 1;
        $dbProduct = new core_db_Product();
        $saleProducts = $dbProduct->queryProductList($query, array("sort"=>"desc"), 6, 1);
        $this->_params['saleProducts'] = $saleProducts['list'];
    }

    /**
     * 意见反馈
     */
    public function pageFeedBack()
    {
        if ($_POST) {
            $name = isset($_POST['name']) ? core_lib_Comm::getStr($_POST['name']) : '';//姓名
            $telephone = isset($_POST['telephone']) ? core_lib_Comm::getStr($_POST['telephone']) : '';//电话
            $suggest = isset($_POST['suggest']) ? core_lib_Comm::getStr($_POST['suggest']) : '';//建议

            $data['name'] = $name;
            $data['telephone'] = $telephone;
            $data['suggest'] = $suggest;

            core_lib_Comm::sendMail(TOMAIL, FROMMAIL, $data);
            return $this->alert(array("status"=>"success", "msg"=>"您的建议我们已收到，我们会尽快联系您！"));
        } else {
            return $this->alert(array("status"=>"error", "msg"=>"接口调用错误"));
        }
    }
}
