<?php

/**
 * 管理员管理相关
 * Class index_manager
 */
class index_manager extends index_base
{
    public function __construct()
    {
        parent::__construct();
        $this->_params['pageTitle'] = "管理员管理";
    }

    /**
     * 管理员列表
     */
    public function pageList()
    {
        $managerId = isset($_GET['managerId']) ? core_lib_Comm::getStr($_GET['managerId'], 'int') : 0;//管理员ID
        $managerName = isset($_GET['managerName']) ? core_lib_Comm::getStr($_GET['managerName']) : '';//管理名称
        $trueName = isset($_GET['trueName']) ? core_lib_Comm::getStr($_GET['trueName']) : '';//真实名称
        $isLock = isset($_GET['isLock']) ? core_lib_Comm::getStr($_GET['isLock'], 'int') : 0;//是否锁定 1 未锁定 2 锁定
        $page = isset($_GET['page']) ? core_lib_Comm::getStr($_GET['page'], 'int') : 0;//页码
        $limit = isset($_GET['limit']) ? core_lib_Comm::getStr($_GET['limit'], 'int') : 0;//间隔值
        $sort = isset($_GET['sort']) ? core_lib_Comm::getStr($_GET['sort'], 'int') : 0;//排序字段 1 管理员ID 2 创建时间 3 上次登录时间
        $isDesc = isset($_GET['isDesc']) ? core_lib_Comm::getStr($_GET['isDesc'], 'int') : 0;//是否倒序 2 倒序 1 正序
        $page = $page ? $page : 1;
        $limit = $limit ? $limit : 10;

        if ($managerId) {
            $query['manager_id'] = $managerId;
        }
        if ($managerName) {
            $query['manager_name'] = $managerName;
        }
        if ($trueName) {
            $query['true_name'] = $trueName;
        }
        if ($isLock) {
            $query['status'] = $isLock - 1;
        }
        $query['sort'] = $sort;
        $query['isDesc'] = $isDesc;

        $dbManager = new core_db_Manager();
        $managers = $dbManager->getManagerList($query, $limit, $page);

        $this->pageBar($managers['total'], $limit, $page, '/manager/list');

        $this->_params['managers'] = $managers['data'];
        $this->_params['total'] = $managers['total'];
        $this->_params['page'] = $page;
        $this->_params['limit'] = $limit;
        $this->_params['managerId'] = $managerId;
        $this->_params['managerName'] = $managerName;
        $this->_params['trueName'] = $trueName;
        $this->_params['isLock'] = $isLock;
        $this->_params['actTitle'] = "管理员列表";
        $this->_params['act'] = "mangerList";
        return $this->render("boss/manager/list.html", $this->_params);
    }

    /**
     * 管理员添加/编辑
     */
    public function pageAction()
    {
        $isEdit = isset($_GET['isEdit']) ? core_lib_Comm::getStr($_GET['isEdit'], 'int') : 0;//是否编辑 1 编辑 0 添加
        $dbManager = new core_db_Manager();
        if ($isEdit) {
            $managerId = isset($_GET['managerId']) ? core_lib_Comm::getStr($_GET['managerId'], 'int') : 0;//管理员ID
            if (!$managerId) {
                return $this->alert(array('status'=>'error','msg'=>"缺少管理员ID"));
            }
            $manager = $dbManager->getManagerById($managerId);
            $this->_params['manager'] = $manager;
        }
        if ($_POST) {
            $managerName = isset($_POST['managerName']) ? core_lib_Comm::getStr($_POST['managerName']) : '';//管理名称
            $trueName = isset($_POST['trueName']) ? core_lib_Comm::getStr($_POST['trueName']) : '';//真实名字
            if (!$isEdit) {
                $password = isset($_GET['password']) ? core_lib_Comm::getStr($_GET['password']) : '';//密码
                $data['password'] = $password;
            } else {
                $data['manager_id'] = $managerId;
            }
            $data['manager_name'] = $managerName;
            $data['true_name'] = $trueName;

            if ($isEdit) {
                $msg = "编辑";
                $managerRS = $dbManager->edit($data, 1);
            } else {
                $msg = "添加";
                $managerRS = $dbManager->add($data);
            }
            if ($managerRS === false) {
//                return $this->alert(array('status'=>'error','msg'=>$msg."失败"));
            }
//            return $this->alert(array('status'=>'error','msg'=>$msg."成功"));
        }
        $this->_params['isEdit'] = $isEdit;
        return $this->render("boss/manager/action.html", $this->_params);
    }

    /**
     * 管理员锁定／解锁 isLock 1 锁定 0 解锁
     */
    public function pageLock()
    {
        $managerId = isset($_GET['managerId']) ? core_lib_Comm::getStr($_GET['managerId'], 'int') : 0;//管理员ID
        if (!$managerId) {
            return $this->alert(array('status'=>'error','msg'=>"缺少管理员ID"));
        }
        if ($_POST) {
            $isLock = isset($_POST['isLock']) ? core_lib_Comm::getStr($_POST['isLock'], 'int') : 1;//锁定
            $data['manager_id'] = $managerId;
            $data['status'] = $isLock;
            $dbManager = new core_db_Manager();
            $managerRS = $dbManager->edit($data);
            $msg = $isLock ? "锁定" : "解锁";
            if ($managerRS === false) {
//                return $this->alert(array('status'=>'error','msg'=>"管理员".$msg."失败"));
            }
//            return $this->alert(array('status'=>'error','msg'=>"管理员".$msg."成功"));
        }
    }

    /**
     * 修改密码
     */
    public function pageEditPassword()
    {
       /* $managerId = isset($_GET['managerId']) ? core_lib_Comm::getStr($_GET['managerId'], 'int') : 0;//管理员ID
        if (!$managerId) {
            return $this->alert(array('status'=>'error','msg'=>"缺少管理员ID"));
        }*/
        if ($_POST) {
            $password = isset($_POST['password']) ? core_lib_Comm::getStr($_POST['password'], 'int') : 1;//密码
            $dbManager = new core_db_Manager();
            $managerRS = $dbManager->modifyPassword($this->_managerId, $password);
            if ($managerRS === false) {
                return $this->alert(array('status'=>'error','msg'=>"修改密码失败"));
            }
            return $this->alert(array('status'=>'error','msg'=>"修改密码成功"));
        }
        return $this->render("", $this->_params);
    }

    /**
     * 重置密码
     */
    public function pageResetPassword()
    {
        $managerId = isset($_GET['managerId']) ? core_lib_Comm::getStr($_GET['managerId'], 'int') : 0;//管理员ID
        if (!$managerId) {
            return $this->alert(array('status'=>'error','msg'=>"缺少管理员ID"));
        }
        if ($_POST) {
            $dbManager = new core_db_Manager();
            $managerRS = $dbManager->modifyPassword($this->_managerId, MANAGER_PASSWD);
            if ($managerRS === false) {
                return $this->alert(array('status'=>'error','msg'=>"重置密码失败"));
            }
            return $this->alert(array('status'=>'error','msg'=>"重置密码成功"));
        }
        return $this->render("", $this->_params);
    }
}
