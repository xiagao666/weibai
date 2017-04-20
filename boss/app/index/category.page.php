<?php

class index_category extends index_base
{
    public function __construct()
    {
//        parent::__construct();
    }

    /**
     * 类别列表
     */
    public function pageList($inPath)
    {
        //查询类目信息
        $dbCategory = new core_db_Category();
        $pCategoryId = isset($_GET["parentCategoryId"]) ? core_lib_Comm::getStr($_GET["parentCategoryId"],
            'int') : 0;
        $pCondition['pid'] = 0;
        $pCategorys = $dbCategory->queryAllCategory($pCondition, CATEGORY_SEL_NUM, 0);

        if($pCategoryId == 0) {
            $params['categoryData'] = $pCategorys['items'];
        }else{
            $query['pid'] = $pCategoryId;
            $categorys = $dbCategory->queryAllCategory($query,CATEGORY_SEL_NUM, 0);
            $params['categoryData'] = $categorys['items'];
        }
        $params['pCategorys'] = $pCategorys['items'];
        $params['parentCategoryId'] = $pCategoryId;
        return $this->render("category/list.html", $params);
    }

    /**
     * 根据父ID查询子类目信息
     */
    public function pageGetChildsByParentId($inPath) {
        $categoryId = $_GET['categoryId'];
        $dbCategory = new core_db_Category();
        $condition['pid'] = $categoryId;
        $rs = $dbCategory->queryAllCategory($condition);
        $params['categorys'] = $rs['items'];
        echo json_encode($params);
    }

    /**
     * 添加/编辑类别
     */
    public function pageActionCategory()
    {
        $isEdit = isset($_GET['isEdit']) ? core_lib_Comm::getStr($_GET['isEdit'], 'int') : 0;//是否编辑 1 编辑 0 添加
        $dbCategory = new core_db_Category();
        $msg = "添加";
        if ($isEdit) {
            $categoryId = isset($_GET['categoryId']) ? core_lib_Comm::getStr($_GET['categoryId'], 'int') : 0;//分类ID
            if (!$categoryId) {
//                return $this->alert(array('status'=>'error','msg'=>"缺少分类ID"));
                core_lib_Comm::p("缺少分类ID");exit;
            }
            $category = $dbCategory->getCategoryById($categoryId);
            $msg = "编辑";
            if ($category === false) {
//                    return $this->alert(array('status'=>'error','msg'=>$msg."失败，编辑的分类不存在"));
                core_lib_Comm::p($msg."失败，编辑的分类不存在error");exit;
            }
            $params['category'] = $category;
        }
        $categoryCondition['pid'] = 0;
        $pCategorys = $dbCategory->queryAllCategory($categoryCondition, CATEGORY_SEL_NUM, 0);
        core_lib_Comm::p($pCategorys);

        if ($_POST) {
            $categoryName = isset($_POST['categoryName']) ? core_lib_Comm::getStr($_POST['categoryName']) : '';//分类名称
            $pid = isset($_POST['pid']) ? core_lib_Comm::getStr($_POST['pid'], 'int') : '';//分类父ID
            $des = isset($_POST['des']) ? core_lib_Comm::getStr($_POST['des']) : '';//分类描述
            $showSort = isset($_POST['showSort']) ? core_lib_Comm::getStr($_POST['showSort'], 'int') : '';//同分类下排序 值越大 越排到前面

            $checkCategory = $dbCategory->getCategoryByName($categoryName);
            if ($isEdit) {
                //编辑的分类名称不能重复
                if ($checkCategory && (int)$checkCategory['pid'] === (int)$pid && (int)$checkCategory['id'] != $categoryId ) {
//                    return $this->alert(array('status'=>'error','msg'=>$msg."失败，不能同一个级别下添加相同的分类"));
                    core_lib_Comm::p($msg."失败，不能同一个级别下添加相同的分类error");exit;
                }
            } else {
                //添加 进行重复分类判断
                if ($checkCategory && (int)$checkCategory['pid'] === (int)$pid ) {
//                    return $this->alert(array('status'=>'error','msg'=>$msg."失败，不能同一个级别下添加相同的分类"));
                    core_lib_Comm::p($msg."失败，不能同一个级别下添加相同的分类error");exit;
                }
            }

            $data['name'] = $categoryName;
            $data['pid'] = $pid;
            $data['des'] = $des;
            $data['show_sort'] = $showSort;
            if ($isEdit) {
                $data['id'] = $categoryId;
                $rs = $dbCategory->updateCategoryById($data);
            } else {
                $rs = $dbCategory->addCategory($data);
            }
            if ($rs === false) {
//                return $this->alert(array('status'=>'error','msg'=>$msg."失败"));
                core_lib_Comm::p($msg."失败error");exit;
            }
//            return $this->alert(array('status'=>'success','msg'=>$msg."成功"));
            core_lib_Comm::p($msg."成功success");exit;
        }

        $params['isEdit'] = $isEdit;
        $params['pCategorys'] = $pCategorys['items'];
        return $this->render('', $params);
    }

    /**
     * 删除分类
     */
    public function delCategory()
    {
        if ($_POST) {
            $categoryId = isset($_GET['categoryId']) ? core_lib_Comm::getStr($_GET['categoryId'], 'int') : 0;//分类ID
            if (!$categoryId) {
//                return $this->alert(array('status'=>'error','msg'=>"缺少分类ID"));
                core_lib_Comm::p("缺少分类ID");exit;
            }
            $dbCategory = new core_db_Category();
            $delRS = $dbCategory->delCategoryById($categoryId);
            if ($delRS === true) {
//                return $this->alert(array('status'=>'success','msg'=>"删除成功"));
                core_lib_Comm::p("删除成功");exit;
            }
//            return $this->alert(array('status'=>'error','msg'=>"删除失败，".$dbCategory->getError("msg")));
            core_lib_Comm::p("删除失败，".$dbCategory->getError("msg"));exit;
        }
    }
}
