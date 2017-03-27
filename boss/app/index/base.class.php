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
        /*if (lib_comm::isSignIn() === false) {//未登录，请先登录
            $this->redirect(SRoute::createUrl('/index.php/login/in', '', 'cp'));
        } else {
            $this->_cp_user_id = $_SESSION['cp_user']['cp_user_id'];
            $this->_username = $_SESSION['cp_user']['username'];
            $mdl_cp_user = SModel::init('core_model_cp_user', $this->_cp_user_id);
            $cp_user = $mdl_cp_user->get();
            $this->_cp_user = $cp_user;
            $this->assign('_cp_user_id', $this->_cp_user_id);
            $this->assign('_username', $this->_username);
            $this->assign('_cp_user', $cp_user);
        }

        //获取来源
        $referer = $_REQUEST['referer'] ? $_REQUEST['referer'] : (string)$_SERVER['HTTP_REFERER'];
        if (preg_match('/' . preg_quote(CP_URL . '/login', '/') . '/', $referer) || !preg_match('/' . preg_quote(CP_URL,
                    '/') . '/', $referer)
        ) {
            $referer = '/';
        }
        $this->_referer = $referer;
        $this->assign('_referer', $referer);*/

        $this->_time = time();
        $this->_ymd = date("Y-m-d", time());


        $this->assign('_ymd', $this->_ymd);
        $this->assign('_time', $this->_time);
    }

    function __destruct()
    {
        //define('DEBUG', TRUE);
        /*$mdl_cplog = SModel::init('core_model_cp_log');
        $mdl_cplog->set('cp_user_id',(int)$this->_cp_user_id);
        $mdl_cplog->set('username',(string)$this->_username);
        $mdl_cplog->set('post',(string)json_encode($_POST));
        $mdl_cplog->set('get',(string)json_encode($_GET));
        $mdl_cplog->set('path_info',(string)($_REQUEST["PATH_INFO"]?$_REQUEST["PATH_INFO"]:$_SERVER['PATH_INFO']));
        $mdl_cplog->set('controller',(string)$this->_path[1]);
        $mdl_cplog->set('action',(string)$this->_path[2]);
        $mdl_cplog->set('ip',SUtil::getIP(true));
        $mdl_cplog->set('create_time',$this->_time);
        $mdl_cplog->set('create_ymd',$this->_ymd);
        $mdl_cplog->save(false);*/
    }
}