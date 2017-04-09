<?php

class index_main extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    function __destruct()
    {
    }

    /**
     * 首页
     */
    public function pageIndex($inPath)
    {
        return $this->render("boss/productList.html");
    }
}
