<?php

class index_main extends index_sign
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
    public function pageIndex($inPath)
    {
        phpinfo();exit;
        $data['id'] = 1;
        $data['name'] = "test";
        $data['pid'] = 1;
        $data['des'] = "des";
        $data['show_sort'] = 1;
        $dbCategory = new core_db_category();
        $dbCategory->add($data);
        echo $this->render("head.tpl");
        echo $this->render("index/index.tpl");
        echo $this->render("footer.tpl");
    }

    public function pageTest()
    {
        echo "test";exit;
    }
}
