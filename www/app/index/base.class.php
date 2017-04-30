<?php

/**
 * 基础类
 * Class index_base
 */
class index_base extends STpl
{
    public $_params;
    public $_dbCms;
    public $_productViewLogQid;

    public function __construct()
    {
        $this->_dbCms = new core_db_Cms();
        $this->getLogo();//logo设置
        $this->getNav();//导航
        $this->getRollImg();//轮播图片
        $this->getCategorys();//分类
        $this->getProductViewLogQid();//浏览产品记录串
    }

    /**
     * logo更换
     */
    public function getLogo()
    {
        $query['type'] = 8;
        $logo = $this->_dbCms->queryNews($query, 1, 1);
        $this->_params['logo'] = $logo['data'][0];
    }

    /**
     * 导航
     */
    public function getNav()
    {
        $query['type'] = 7;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $navs = $this->_dbCms->queryNews($query, 10, 1);
        $this->_params['navList'] = $navs['data'];
    }

    /**
     * 轮播图
     */
    public function getRollImg()
    {
        $query['type'] = 6;
        $query['sort'] = 2;//sort 排序
        $query['isDesc'] = 2;//倒序
        $rolls = $this->_dbCms->queryNews($query, 6, 1);
        $this->_params['rollList'] = $rolls['data'];
    }

    /**
     * 产品分类
     */
    public function getCategorys()
    {
        $dbCategory = new core_db_Category();
        $parentCategorys = $dbCategory->queryAllCategory(array("pid"=>0), 10000, 1);
        if (is_array($parentCategorys['items'])) {
            foreach ( $parentCategorys['items'] as $cpk => $cpv ) {
                $categoryList[$cpv['id']] = $cpv;
            }
        }
        $categorys = $dbCategory->queryAllCategory(array("pid > 0"), 10000, 1);
        if (is_array($categorys['items'])) {
            foreach ($categorys['items'] as $ck => $cv) {
                $categoryList[$cv['pid']]['son'][$cv['id']] = $cv;
            }
        }
        $this->_params['categoryList'] = $categoryList;
    }

    /**
     * 产品浏览记录串
     */
    public function getProductViewLogQid()
    {
        $this->_productViewLogQid = $_SESSION['viewLog'];
        if (!$this->_productViewLogQid) {
            $_SESSION['viewLog'] = uniqid();
        }
    }

    /**
     * 分页
     * @param $totalSize 总条数
     * @param $limit 每页显示的数
     * @param $currPage 当前页
     * @param $url 当前页面地址
     */
    public function pageBar($totalSize, $limit, $currPage, $url)
    {
        $totalPage = min(ceil($totalSize / $limit), 500);
        $currPage = min($totalPage, $currPage);
        $currPage = $currPage ? $currPage : 1;
        $prePage = $currPage - 1;
        $prePage = $prePage ? $prePage : 1;
        $nextPage = $currPage >= $totalPage ? '' : $currPage + 1;

        $pageStart = max(min($currPage - 2, $totalPage - PAGE_SIZE), 1);
        $pageEnd = min(($pageStart - 1 + PAGE_SIZE), $totalPage);

        $queryStr = $_SERVER ['QUERY_STRING'];
        parse_str($queryStr, $queryRow);
        if ($pageStart != 1) {
            $this->_params['pages'][1]['page'] = 1;
            $queryRow['page'] = $this->_params['pages'][1]['page'];
            $this->_params['pages'][1]['url'] = $url."?".http_build_query($queryRow);
        }
        if ($pageStart >= 3) {
            $this->_params['pages'][2]['page'] = "...";
            $this->_params['pages'][2]['url'] = '';
        }
        for ($i = $pageStart; $i <= $pageEnd; $i++) {
            $this->_params['pages'][$i]['page'] = $i;
            $queryRow['page'] = $this->_params['pages'][$i]['page'];
            $this->_params['pages'][$i]['url'] = $url."?".http_build_query($queryRow);
        }
        if ($pageEnd <= $totalPage - PAGE_SIZE + 2) {
            $this->_params['pages'][$totalPage-1]['page'] = "...";
            $this->_params['pages'][$totalPage-1]['url'] = '';
        }
        $this->_params['pages'][$totalPage]['page'] = $totalPage;
        $queryRow['page'] = $totalPage;
        $this->_params['pages'][$totalPage]['url'] = $url."?".http_build_query($queryRow);

        $this->_params['totalSize'] = $totalSize;
        $this->_params['currPage'] = $currPage;
        $this->_params['totalPage'] = $totalPage;
        $this->_params['limit'] = $limit;
        $this->_params['prePage'] = $prePage;
        $this->_params['prePageUrl'] = $queryStr ? $url."&page=".$prePage : "?page=".$prePage;
        $this->_params['nextPage'] = $nextPage;
        $this->_params['nextPageUrl'] = $queryStr ? $url."&page=".$nextPage : "?page=".$nextPage;
    }

    public function __destruct()
    {
    }
}