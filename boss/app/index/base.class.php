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

    public function __construct()
    {
        //判读登录
        if (lib_comm::isSignIn() === false) {//未登录，请先登录
            $this->redirect("/sign/in");
        } else {
            $this->_managerId = $_SESSION['manager']['managerId'];
            $this->_managerName = $_SESSION['manager']['managerName'];

            $dbManager = new core_db_manager();
            $manager = $dbManager->getManagerById($this->_managerId);
            $this->_manager = $manager;
            $this->assign('_managerId', $this->_managerId);
            $this->assign('_managerName', $this->_managerName);
            $this->assign('_manager', $manager);
        }

        //获取来源
        $referer = $_REQUEST['referer'] ? $_REQUEST['referer'] : (string)$_SERVER['HTTP_REFERER'];
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
    }

    public function __destruct()
    {
    }
}