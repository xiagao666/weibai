<?php

/**
 * Class upfile
 */
class SUploadFile
{
    public $filepath = '../../file/pdimage/'; //上传文件存放路径

    public $filesize = 10485760; //允许上传的大小

    public $india = false; //是否打水印 true打 false不打

    public $indiaPath = '';//水印地址

    public $position = 3;//水印位置 1 = left-top, 2 = right-top, 3 = right-bottom, 4 = left-bottom, 5 = center

    public $margin = 1;//缩略图的边界 margin to the border of the thumbnail

    public $uri;//uri地址

    public $isThumbnail;//是否缩略图

    /**
     * 设置图片存放路径
     */
    public function setFilePath($filePath)
    {
        $this->filepath = $filePath;
    }

    /**
     * 获取图片存放路径
     * @return string
     */
    public function getFilePath()
    {
        return $this->filepath;
    }

    /**
     * 设置uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * 获取uri
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * 设置是否打水印
     */
    public function setIndia($india = false)
    {
        $this->india = $india;
    }

    /**
     * 获取是否打水印
     */
    public function getIndia()
    {
        return $this->india;
    }

    /**
     * 设置水印地址
     */
    public function setIndiaPath($indiaPath = '')
    {
        $this->indiaPath = $indiaPath;
    }

    /**
     * 获取水印地址
     */
    public function getIndiaPath()
    {
        return $this->indiaPath;
    }

    /**
     * 设置水印位置
     */
    public function setPosition($position = 3)
    {
        $this->position = $position;
    }

    /**
     * 获取水印位置
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * 设置缩略图的边界
     */
    public function setMargin($margin = 1)
    {
        $this->margin = $margin;
    }

    /**
     * 获取缩略图的边界
     */
    public function getMargin()
    {
        return $this->margin;
    }

    /**
     * 是否设置缩略图
     */
    public function setIsThumbnail($isThumbnail = false)
    {
        $this->isThumbnail = $isThumbnail;
    }

    /**
     * 返回是否设置缩略图
     */
    public function getIsThumbnail()
    {
        return $this->isThumbnail;
    }

    /**
     * 上传图片
     */
    public function uploadfile($upfile)
    {
        if ($upfile == "") {
            die("uploadfile:参数不足");
        }

        $upfileType = $upfile['type'];
        $upfileSize = $upfile['size'];
        $upfileTmpName = $upfile['tmp_name'];
        $upfileError = $upfile['error'];
        if ($upfileSize > $this->filesize) {
            return false; //文件过大
        }

        switch ($upfileType) { //文件类型
            case 'image/jpeg' :
                $type = 'jpg';
                break;
            case 'image/pjpeg' :
                $type = 'jpg';
                break;
            case 'image/png' :
                $type = 'png';
                break;
            case 'image/gif' :
                $type = 'gif';
                break;
        }
        if (!isset ($type)) {
            return false; //不支持此类型
        }
        if (!is_uploaded_file($upfileTmpName) or !is_file($upfileTmpName)) {
            return false; //文件不是经过正规上传的;
        }
        if ($upfileError != 0) {
            return false; //其他错误
        }

        if ((int)$upfileError === 0) {
            if (!file_exists($upfileTmpName)) {
                return false; //临时文件不存在
            } else {
                $fileName = date("ymdhis", time()) . "_s";//s 代表原图
                $url = "/" . $this->getUri() . "/";
                $dfile = date("Y-m-d") . "/";
                $this->filepath .= $dfile;
                $url .= $dfile;
                if (!file_exists($this->filepath)) {
                    mkdir($this->filepath);
                }
                $newFileName = $this->filepath . $fileName . "." . $type;
                $url .= $fileName . "." . $type;

                if (!move_uploaded_file($upfileTmpName, $newFileName)) {
                    return false; //文件在移动中丢失
                } else {
                    $fileInfo = array(
                        "originalName" => $upfile['name'],
                        "name" => $fileName,
                        "path" => $newFileName,
                        "url" => $url,
                        "size" => $upfileSize,
                        "type" => $type,
                        "status" => "success"
                    );
                    unlink($upfileTmpName);
                    return $fileInfo; //上传成功!
                }
            }
        }
    }

    /**
     * 生成缩略图
     * $param $thumbSize array(
     *  array(
     *      'tMaxWidth' => 100,
     *      'tMaxHeight' => 100
     *      ),
     * array(
     *      'tMaxWidth' => 101,
     *      'tMaxHeight' => 101
     *      ),
     * .......多个缩略图 传入多个
     * );
     */
    public function createThumbnail($filePath, $thumbSize)
    {
        if (!is_array($thumbSize)) {//缩略图最大尺寸
            return false;
        }
        $thumbnail = new SThumbnail($filePath, 100);

        if ($this->india) {
            $thumbnail->addLogo($this->indiaPath, $this->position, $this->margin);
        }
        list($sWidth, $sHeight, $sImageType, $des) = getimagesize($filePath);
        $pathInfo = explode("/", $filePath);
        $oldFileName = $pathInfo[count($pathInfo) - 1];
        unset($pathInfo[count($pathInfo) - 1]);
        $thumbPath = implode("/", $pathInfo);
        $oldFileNames = explode("_", $oldFileName);
        $thumbFileName = $oldFileNames[0] . "_t_";
        $thumbSuffixs = explode(".", $oldFileNames[1]);
        $thumbSuffix = $thumbSuffixs[count($thumbSuffixs) - 1];
        //等比例缩放
        foreach ($thumbSize as $k => $v) {
            $tMaxWidth = $v['tMaxWidth'];
            $tMaxHeight = $v['tMaxHeight'];
            if (($tMaxWidth && $sWidth > $tMaxWidth) || ($tMaxHeight && $sHeight > $tMaxHeight)) {
                if ($tMaxWidth && $sWidth > $tMaxWidth) {//计算宽度的比例
                    $widthRatio = $tMaxWidth / $sWidth;
                    $resizeWithTag = true;
                }
                if ($tMaxHeight && $sHeight > $tMaxHeight) {//计算高度的比例
                    $heightRatio = $tMaxHeight / $sHeight;
                    $resizeHeightTag = true;
                }
                if ($widthRatio && $heightRatio) {//对比宽比和高比 取小值
                    if ($widthRatio < $heightRatio) {
                        $ratio = $widthRatio;
                    } else {
                        $ratio = $heightRatio;
                    }
                }
                if ($resizeWithTag && !$resizeHeightTag) {
                    $ratio = $widthRatio;
                }
                if (!$resizeWithTag && $resizeHeightTag) {
                    $ratio = $heightRatio;
                }
                $nWidth = round($sWidth * $ratio);//计算新宽度
                $nHeight = round($sHeight * $ratio);//计算新高度
            }
            $thumbnail->setMaxSize($nWidth, $nHeight);//设置缩略图新宽 高
            $thumbnail->setQualityOutput(true);
            $thumbUrl = "/" . $pathInfo[3] . "/" . $pathInfo[4] . "/";
            $thumbName = $thumbFileName . $v['tMaxWidth'] . "_" . $v['tMaxHeight'] . "." . $thumbSuffix;
            $thumbnail->genFile($thumbPath . "/" . $thumbName);//生成缩略图
            return $thumbUrl . $thumbName;
        }
    }

    /**
     * 文档上传
     */
    public function uploadDoc($upfile)
    {
        if ($upfile == "") {
            die("uploadfile:参数不足");
        }

        $upfileType = $upfile['type'];
        $upfileSize = $upfile['size'];
        $upfileTmpName = $upfile['tmp_name'];
        $upfileError = $upfile['error'];
        if ($upfileSize > $this->filesize) {
            return false; //文件过大
        }

        switch ($upfileType) {//文件类型
            case 'application/msword':
                $type = "doc";
                break;
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $type = "docx";
                break;
            case 'application/vnd.ms-excel':
                $type = "xls";
                break;
            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $type = "xlsx";
                break;
            case 'application/pdf':
                $type = "pdf";
                break;
        }
        if (!isset ($type)) {
            return false; //不支持此类型
        }
        if (!is_uploaded_file($upfileTmpName) or !is_file($upfileTmpName)) {
            return false; //文件不是经过正规上传的;
        }
        if ($upfileError != 0) {
            return false; //其他错误
        }

        if ((int)$upfileError === 0) {
            if (!file_exists($upfileTmpName)) {
                return false; //临时文件不存在
            } else {
                $fileName = date("ymdhis", time());
                $url = "/" . $this->getUri() . "/";
                $dfile = date("Y-m-d") . "/";
                $this->filepath .= $dfile;
                $url .= $dfile;
                if (!file_exists($this->filepath)) {
                    mkdir($this->filepath);
                }
                $newFileName = $this->filepath . $fileName . "." . $type;
                $url .= $fileName . "." . $type;

                if (!move_uploaded_file($upfileTmpName, $newFileName)) {
                    return false; //文件在移动中丢失
                } else {
                    $fileInfo = array(
                        "originalName" => $upfile['name'],
                        "name" => $fileName,
                        "path" => $newFileName,
                        "url" => $url,
                        "size" => $upfileSize,
                        "type" => $type,
                        "status" => "success"
                    );
                    unlink($upfileTmpName);
                    return $fileInfo; //上传成功!
                }
            }
        }
    }
}
