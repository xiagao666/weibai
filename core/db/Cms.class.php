<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/25
 * Time: 19:41
 */
class core_db_Cms extends core_db_DbBase
{

    private $table = "vb_cms";

    /**
     * core_db_news constructor.
     */
    public function __construct()
    {
        parent::__construct($this->table);
    }

    /**
     * 添加cms
     * @param $data
     * @return bool|int
     */
    public function addNews($data)
    {
        try {
            if (empty($data)) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common", "main");
            $rs = $this->insertData($data);
            if ($rs === false) {
                throw new Exception("添加记录失败");
            }
            return $rs;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 查询cms列表
     * @param $query
     * @param $page
     * @param $limit
     * @param $orderBy
     * @return
     */
    public function queryNews($query, $limit = 10, $page = 0)
    {
        try {
            $sort = isset($query['sort']) ? $query['sort'] : 1;
            unset($query['sort']);
            $isDesc = isset($query['isDesc']) ? $query['isDesc'] : 2;
            unset($query['isDesc']);
            $orderby = '';
            switch ($sort) {
                case 1://CmsID
                    $orderby .= "id";
                    break;
                case 2://排序 越大越在前面
                    $orderby .= "sort";
                    break;
            }
            switch ($isDesc) {
                case 1://正序
                    $orderby .= " ASC";
                    break;
                case 2://倒序
                    $orderby .= " DESC";
                    break;
            }

            $this->useConfig("common", "query");
            $rs = $this->getAllData($query, "*", "", $orderby, "", $limit, $page);
            if ($rs === false) {
                throw new Exception("获取数据为空或者失败");
            }
            $list['total'] = $rs->totalSize;
            $list['data'] = $rs->items;
            return $list;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 通过ID获取CMS
     * @param $param
     * @return bool
     */
    public function getCmsById($cmsId)
    {
        try {
            if (!$cmsId) {
                throw new Exception("缺少必要参数");
            }

            $this->useConfig("common", "query");
            $rs = $this->getOne(array("id" => $cmsId), "*");
            if ($rs === false) {
                throw new Exception("获取数据为空或者失败");
            }
            return $rs;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 更新
     * @param $condition
     * @param $item
     * @return bool
     */
    public function updateCmsById($data)
    {
        try {
            $id = $data['id'];
            unset($data['id']);
            if (!$id) {
                throw new Exception("缺少必要参数");
            }

            $data['last_update_date'] = date("Y-m-d H:i:s", time());
            $this->useConfig("common", "main");
            $rs = $this->updateData( array('id'=>$id), $data);
            if ($rs === false) {
                throw new Exception("更新失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 删除内容
     * @param $condition
     * @return bool
     */
    public function deleteCmsById($cmsId)
    {
        try {
            if (!$cmsId) {
                throw new Exception("缺少必要参数");
            }

            $cms = $this->getCmsById($cmsId);
            if ($cms === false) {
                throw new Exception("删除内容不存在");
            }
            $this->useConfig("common", "main");
            $rs = $this->deleteData(array('id'=>$cmsId));
            if ($rs === false) {
                throw new Exception("删除失败");
            }
            return true;
        } catch (Exception $e) {
            $this->log($e);
            return false;
        }
    }

    /**
     * 根据类型获取类型名称
     */
    public function getTypeNameByType($type = 0)
    {
        $types = array(
            0 => '无',
            1 => '公司新闻',
            2 => '代理品牌',
            3 => '技术服务',
            4 => '关于唯佰',
            5 => '联系我们',
            6 => '轮播图片',
            7 => '导航',
            8 => '整站logo',
//            9 => '首页-产品促销-大图',
//            10 => '首页-新闻资讯-左图',
        );
        if ($type) {
            return $types[$type];
        }
        return $types;
    }
}