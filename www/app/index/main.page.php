<?php

class index_main extends STpl
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
        $
        // echo "首页";exit;
        /*echo "11";
        $data['id'] = 1;
        $data['name'] = "test";
        $data['pid'] = 1;
        $data['des'] = "des";
        $data['show_sort'] = 1;
        $dbCategory = new core_db_category();
        //$dbCategory->add($data);
        echo $this->render("head.tpl");
        echo $this->render("index/index.tpl");
        echo $this->render("footer.tpl");*/
        if ($_POST) {//$_POST $_GET $_FILE['name'] $_REQUEST

        }
        $params["hello"] = "xxxxxxxx";
        $params["keywords"] = "hahfhahfha";
        return $this->render("index/index.html", $params);
    }
    function pageNews($inPath){
        $dbNews = new core_db_news();
        $rs = $dbNews->queryNews($param="",2,1);
        echo '<pre>';
        var_dump($rs);
        echo '</pre>';
    }
    public function pageTest()
    {
        echo "test";exit;
    }
}
