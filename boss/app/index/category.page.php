<?php

class index_category extends index_base
{
    function __construct()
    {
    }

    function __destruct()
    {
    }

    /**
     * 首页
     */
    public function pageList($inPath)
    {
        //查询类目信息
        $dbCategory = new core_db_Category();
        $condition["pid"] = 0;
        $rs = $dbCategory->queryAllCategory($condition);
        $condition["pid"] != 0;
        $rsItems = $dbCategory->queryAllCategory($condition);
        foreach ($rs as $k => $item) {
            foreach ($rsItems as $key => $rv) {
                if ( $item['id'] == $rv['pid'] ) {
                    $rs[$k]['son'][$key] = $rv;
                }
            }
        }
        return $this->render("boss/category.html");
    }

    public function pageTest()
    {
        echo "test";exit;
    }
}
