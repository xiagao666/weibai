<?php

/**
 * 上传相关
 * Class index_upload
 */
class index_upload extends index_base
{
    public function __construct()
    {
//        parent::__construct();
    }

    /**
     * 上传图片
     */
    public function pageIndex($inPath)
    {
        $action = isset($_GET['action']) ? core_lib_Comm::getStr($_GET['action']) : '';//图片上传来源
        $upfile = isset($_FILES['upfile']) ? $_FILES['upfile'] : '';
        if (empty($upfile)) {
            return $this->alert(array('status'=>'error','msg'=>"上传的信息不正确"));
        }
        $supload = new SUploadFile();
        $supload->setFilePath("../../file/".$action."/");
        $supload->setUri('edimage');
        $fileInfo = $supload->uploadfile($upfile);
        if ($action == "edimage") {
            exit(WWW_URL.$fileInfo['url']);
        }
    }
}
