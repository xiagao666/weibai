<?php

/**
 * 管理员相关
 * core_db_manager
 */
class core_db_Manager extends core_db_DbBase
{
    private $table = "vb_manager";

    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * 添加管理员
     */
    public function add($data)
    {
        try {
            //判断数据必选项
            if (empty($data)) {
                throw new Exception("缺少必要参数");
            }

            //判断是否重复管理员名称
            $manager = $this->getMangerByManagerName($data['manager_name']);
            if ( $manager !== false ) {
                throw new Exception("管理员名称重复");
            }

            //密码
            $password = md5($data['password'].MANAGER_REG_KEY);
            $data['password'] = $password;

            $data['register_time'] = $this->_time;
            $data['register_ymd'] = $this->_ymd;
            $this->useConfig("common", "main");
            $rs = $this->insertData($data);
            if ($rs === false) {
                throw new Exception("数据写入失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 编辑管理员
     */
    public function edit($data, $isUpadteTime = 0)
    {
        try {
            //判断数据必选项
            if ($data['password']) {
                throw new Exception("不能通过本接口修改密码");
            }
            $manager_id = $data['manager_id'];
            unset($data['manager_id']);
            if (!$manager_id) {
                throw new Exception("缺少管理员ID");
            }
            if (empty($data)) {
                throw new Exception("缺少修改的必要参数");
            }

            //判断是否添加过管理员
            $manager = $this->getManagerById($manager_id);
            if ( $manager === false ) {
                throw new Exception("管理员不存在");
            }

            if ($isUpadteTime) {
                $data['update_time'] = $this->_time;
                $data['update_ymd'] = $this->_ymd;
            }
            $this->useConfig("common", "main");
            $rs = $this->updateData(array("manager_id"=>$manager_id), $data);
            if ($rs === false) {
                throw new Exception("数据更改失败");
            }
            return $rs;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 通过管理员ID获取管理员信息
     */
    public function getManagerById($managerId)
    {
        try {
            //判断数据必选项
            if (!$managerId) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common", "query");
            $rs = $this->getOne(array("manager_id"=>$managerId), "*");
            if ($rs === false) {
                throw new Exception("获取数据为空或者失败");
            }
            return $rs;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 通过管理员名称获取管理员信息
     */
    public function getMangerByManagerName($managerName)
    {
        try {
            //判断数据必选项
            if (!$managerName) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common", "query");
            $rs = $this->getOne(array("manager_name"=>$managerName), "*");
            if ($rs === false) {
                throw new Exception("获取数据为空或者失败");
            }
            return $rs;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 获取管理员列表
     */
    public function getManagerList($query, $limit = 10, $page = 0)
    {
        try {
            $sort = $query['sort'];
            $sort = $sort ? $sort : 1;
            unset($query['sort']);
            $isDesc = $query['isDesc'];
            $isDesc = $isDesc ? $isDesc : 2;
            unset($query['isDesc']);
            $orderby = '';
            switch ($sort) {
                case 1://管理员ID
                    $orderby .= "manager_id";
                    break;
                case 2://创建时间
                    $orderby .= "create_time";
                    break;
                case 3://上次登录时间
                    $orderby .= "last_in_time";
                    break;
            }
            switch ($isDesc) {
                case 1://正序
                    $orderby .= " ASC";
                    break;
                case 2://倒序
                    $orderby .= " DESC";
                    break;
            }

            $this->useConfig("common", "query");
            $rs = $this->getAllData($query, "*", "", $orderby, "", $limit, $page);
            if ($rs === false) {
                throw new Exception("获取数据为空或者失败");
            }
            $list['total'] = $rs->totalSize;
            $list['data'] = $rs->items;
            return $list;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 修改密码
     */
    public function modifyPassword($managerId, $newPassword)
    {
        try {
            //判断数据必选项
            if (!$managerId || !$newPassword) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common", "main");
            $manager = $this->getManagerById($managerId);
            if ($manager === false) {
                throw new Exception("获取数据为空或者失败");
            }

            //密码
            $password = md5($newPassword . MANAGER_REG_KEY);
            $data['password'] = $password;

            $this->useConfig("common", "main");
            $managerRS = $this->updateData(array("manager_id" => $managerId), $data);
            if ($managerRS === false) {
                throw new Exception("密码更改失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 登录
     */
    public function sign($managerName, $password)
    {
        try {
            //判断数据必选项
            if (!$managerName || !$password) {
                throw new Exception("缺少必要参数");
            }

            $manager = $this->getMangerByManagerName($managerName);
            if ($manager === false) {
                throw new Exception("获取数据为空或者失败");
            }
            //密码
            $password = md5($password.MANAGER_REG_KEY);
            if ($password !== $manager['password']) {
                throw new Exception("登录失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }


    // 删除用户
    public function delete($managerId){
        try{
            if (!$managerId){
                throw new Exception("缺少必要参数");
            }
            $this->useConfig("common", "main");
            $condition["manager_id"] = $managerId;
            $rs = $this->deleteData($condition);
            if($rs === false) {
                throw new Exception("删除用户失败");
            }
            return true;
        } catch(Exception $e){
            $this->log($e);
            return false;
        }
    }
}
