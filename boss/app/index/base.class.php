<?php

/**
 * 基础类
 * Class index_base
 */
class index_base extends STpl
{
    protected $_managerId;
    protected $_managerName;
    protected $_manager;
    protected $_referer;
    protected $_path;
    protected $_params;

    public function __construct()
    {
        //判读登录
        if (lib_Comm::isSignIn() === false) {//未登录，请先登录
            $this->redirect("/sign/in");
        } else {
            $this->_managerId = $_SESSION['manager']['managerId'];
            $this->_managerName = $_SESSION['manager']['managerName'];
            $dbManager = new core_db_Manager();
            $manager = $dbManager->getManagerById($this->_managerId);
            $this->_manager = $manager;
            $this->assign('_managerId', $this->_managerId);
            $this->assign('_managerName', $this->_managerName);
            $this->assign('_manager', $manager);
        }

        //获取来源
        $referer = isset($_REQUEST['referer']) ? $_REQUEST['referer'] : (string)$_SERVER['HTTP_REFERER'];
        if (preg_match('/' . preg_quote(BOSS_URL . '/sign', '/') . '/', $referer)
            || !preg_match('/' . preg_quote(BOSS_URL, '/') . '/', $referer)) {
            $referer = '/';
        }

        $this->_referer = $referer;
        $this->assign('_referer', $referer);

        $this->_time = time();
        $this->_ymd = date("Y-m-d", time());

        $this->assign('_ymd', $this->_ymd);
        $this->assign('_time', $this->_time);
        $this->_params['headerTitle'] = "唯佰生物";
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
        $queryRow['page'] = $prePage;
        $this->_params['prePage'] = $prePage;
        $this->_params['prePageUrl'] = $url."?".http_build_query($queryRow);
        $queryRow['page'] = $nextPage;
        $this->_params['nextPage'] = $nextPage;
        $this->_params['nextPageUrl'] = $url."?".http_build_query($queryRow);
    }

    public function __destruct()
    {
    }
}