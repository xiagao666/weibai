<?php

/**
 * 产品相关
 */
class index_product extends STpl
{
    function __construct()
    {
    }

    function __destruct()
    {
    }

    /**
     * 产品列表
     */
    public function pageIndex($inPath)
    {
        echo "产品列表";exit;
        /*echo "11";
        $data['id'] = 1;
        $data['name'] = "test";
        $data['pid'] = 1;
        $data['des'] = "des";
        $data['show_sort'] = 1;
        $dbCategory = new core_db_category();
        $dbCategory->add($data);
        echo $this->render("head.tpl");
        echo $this->render("index/index.tpl");
        echo $this->render("footer.tpl");*/
    }

    /**
     * 产品详情
     */
    public function pageDetail($inPath)
    {
        echo "产品详情";exit;
    }
}
