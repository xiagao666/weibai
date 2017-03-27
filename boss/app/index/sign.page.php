<?php

/**
 * 登录／退出
 * Class index_sign
 */
class index_sign extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    /* function __destruct()
     {
     }*/

    /**
     * 登录
     */
    public function pageIn($inPath)
    {
        $dbManager = new core_db_manager();
        $data['manager_id'] = 1;
        $data['manager_name'] = 'test3';
        $data['password'] = 'test2017';
        $data['true_name'] = 'test3';
        $data['status'] = 0;
        $dbManager->edit($data);
        echo "登录";
    }

    /**
     * 退出
     */
    public function pageOut()
    {
        echo "退出";exit;
    }
}
