<?php

class core_service_cpuser extends SService
{
    protected $_master_table = 'core_model_cp_user';

    function __construct($id = 0)
    {
        parent::__construct($id);
    }

    /**
     * 增加/编辑管理员
     */
    public function actionCpUser($data)
    {
        if ($this->_id) {
            $cp_user_info = $this->getCpUserInfo();
            if (empty($cp_user_info)) {
                $this->setError("管理员不存在");
                return false;
            }
            $condition = array("cp_user_id != {$this->_id}");
        }

        $condition['cp_user_name'] = $data['cp_user_name'];
        $cp_user = $this->model()->selectOne($condition);

        if (!empty($cp_user)) {
            $this->setError("管理员名称已存在");
            return false;
        }
        if (!$this->_id) {
            $data['register_time'] = $this->_time;
            $data['register_ymd'] = date('Y-m-d', $this->_time);
            if ($data['cp_password']) {
                $data['cp_password'] = md5($data['cp_password'] . CP_REG_CONST);
            }
        }

        $this->set($data);
        if ($this->save() === false) {
            $this->setError("管理员存储错误");
            return false;
        }
        return true;
    }

    /**
     * 管理员信息
     */
    public function getCpUserInfo()
    {
        return $this->get();
    }

    /**
     * 删除管理员
     */
    public function delCpUser()
    {
        if (!$this->_id) {
            $this->setError("请输入管理员ID");
            return false;
        }
        $cp_user_info = $this->getCpUserInfo();
        if (empty($cp_user_info)) {
            $this->setError("管理员ID不存在");
            return false;
        }
        if ($this->model()->del() === false) {
            $this->setError("删除失败");
            return false;
        }
        return true;
    }

    /**
     * 重置密码
     */
    public function resetCpUser()
    {
        if (!$this->_id) {
            $this->setError("请输入管理员ID");
            return false;
        }
        $cp_user_info = $this->getCpUserInfo();
        if (empty($cp_user_info)) {
            $this->setError("管理员ID不存在");
            return false;
        }

        $cp_password = md5(CP_REG_CONST . CP_REG_CONST);
        $this->_set('cp_password', $cp_password);
        if ($this->save() === false) {
            $this->setError("重置密码失败");
            return false;
        }
        return true;
    }

    /**
     * 禁止管理员登录
     */
    public function lockCpUser($status = 0)
    {
        if (!$this->_id) {
            $this->setError("请输入管理员ID");
            return false;
        }
        $cp_user_info = $this->getCpUserInfo();
        if (empty($cp_user_info)) {
            $this->setError("管理员ID不存在");
            return false;
        }

        $this->_set('status', $status);
        if ($this->save() === false) {
            $this->setError("锁定失败");
            return false;
        }
        return true;
    }

    /**
     * 修改密码
     */
    public function changePassword($old_cp_password, $new_cp_password)
    {
        if (!$this->_id) {
            $this->setError("请输入管理员ID");
            return false;
        }
        $cp_user_info = $this->getCpUserInfo();
        if (empty($cp_user_info)) {
            $this->setError("管理员ID不存在");
            return false;
        }
        if ($cp_user_info['cp_password'] != md5($old_cp_password . CP_REG_CONST)) {
            $this->setError("管理员原密码输入错误");
            return false;
        }

        $cp_password = md5($new_cp_password . CP_REG_CONST);
        $this->_set('cp_password', $cp_password);
        if ($this->save() === false) {
            $this->setError("修改失败");
            return false;
        }
        return true;
    }
}