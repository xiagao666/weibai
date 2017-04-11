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
        $rs = $dbCategory->queryAllCategory($condition," show_sort desc");
        $param["categorys"] = $rs->items;
        $param["columns"] = core_lib_Comm::getTableColumns(CATEGORY_COLUMNS);
        return $this->render("boss/categoryList.html", $param);
    }

    public function pageTest()
    {
        echo "test";exit;
    }
}
