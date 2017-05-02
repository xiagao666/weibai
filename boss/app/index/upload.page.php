<?php

/**
 * 上传相关
 * Class index_upload
 */
class index_upload extends index_base
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 上传图片
     */
    public function pageIndex($inPath)
    {
        $action = isset($_GET['action']) ? core_lib_Comm::getStr($_GET['action']) : '';//图片上传来源
        $upfiles = isset($_FILES['upfile']) ? $_FILES['upfile'] : '';
        if (empty($upfiles)) {
            return $this->alert(array('status'=>'error','msg'=>"上传的信息不正确"));
        }
        $supload = new SUploadFile();
        $supload->setFilePath("../../file/".$action."/");
        $supload->setUri($action);

        switch ($action) {
            case 'pdoc'://产品文档
                $fileInfo = $supload->uploadDoc($upfiles);
                return $this->alert(array('status'=>$fileInfo['status'],'url'=>WWW_URL.$fileInfo['url'], 'name'=>$fileInfo['originalName']));
                break;
            case 'edimage'://富文本图片
                $fileInfo = $supload->uploadfile($upfiles);
                exit(WWW_URL.$fileInfo['url']);
                break;
            case 'pdimage'://产品图片
                $fileInfo = $supload->uploadfile($upfiles);
                $thumbSize = array(//缩略图尺寸
                    array('tMaxWidth'=>THUMBNAIL_W, 'tMaxHeight'=>THUMBNAIL_H)
                );
                $minUrl = $supload->createThumbnail($fileInfo['path'], $thumbSize);
                return $this->alert(array('status'=>$fileInfo['status'],'bgUrl'=>WWW_URL.$fileInfo['url'], 'minUrl'=>WWW_URL.$minUrl));
                break;
            case 'thimage':
                $fileInfo = $supload->uploadfile($upfiles);
                return $this->alert(array('status'=>$fileInfo['status'],'imgUrl'=>WWW_URL.$fileInfo['url']));
                break;
        }
    }
}
