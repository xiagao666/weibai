<?php

/**
 * 管理员相关
 * core_db_manager
 */
class core_db_manager extends core_db_DbBase
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
            $rs = $this->insert($data);
            if ($rs === false) {
                throw new Exception("数据写入失败");
            }
            return $rs;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 编辑管理员
     */
    public function edit($data)
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
            if ( $manager !== false ) {
                throw new Exception("管理员名称重复");
            }

            $data['update_time'] = $this->_time;
            $data['update_ymd'] = $this->_ymd;

            $this->useConfig("common", "main");
            $rs = $this->update(array("manager_id"=>$manager_id), $data);
            if ($rs === false) {
                throw new Exception("数据更改失败");
            }
            return $rs;
        } catch (Exception $e) {
            error_log("");
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
            return false;
        }
    }

    /**
     * 获取管理员列表
     */
    public function getMnagersList()
    {

    }

    /**
     * 重置密码
     */
    public function resetPasswd()
    {

    }

    /**
     * 修改密码
     */
    public function modifyPasswd()
    {

    }
}
