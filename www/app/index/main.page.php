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
        $query["type"] = 3;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $dbCms = new core_db_Cms();
        $techs = $dbCms->queryNews($query, 16, 1);

        $techList = $techs['data'];
        $bigTech = $techList[0];
        unset($techList[0]);

        $this->_params['techs'] = array_values($techList);
        $this->_params['bigTech'] = $bigTech;
        $this->_params['currNav'] = "tech";
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
        $this->_params['currNav'] = "about";
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
        $this->_params['currNav'] = "contact";
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
        $sales = $this->_dbCms->queryNews($query, 1, 1);
        $this->_params['saleImg'] = $sales['data'][0];
    }

    /**
     * 首页-新闻资讯-左图
     */
    public function getNewsLeftImg()
    {
        $query['type'] = 10;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $leftImgs = $this->_dbCms->queryNews($query, 1, 1);
        $this->_params['leftImg'] = $leftImgs['data'][0];
    }

    /**
     * 获取促销商品
     */
    public function getSaleProduct()
    {
        $query['is_sale'] = 1;
        $dbProduct = new core_db_Product();
        $saleProducts = $dbProduct->queryProductList($query, array("sort"=>"desc"), 2, 1);
        $this->_params['saleProducts'] = $saleProducts['list'];
    }
}
