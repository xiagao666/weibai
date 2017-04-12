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
        $pCondition['pid'] = 0;
        $pCategorys = $dbCategory->queryAllCategory($pCondition);

        $condition[] = 'pid > 0';
        $categorys = $dbCategory->queryAllCategory($condition);

        if ($pCategorys['items']) {
            foreach ($pCategorys['items'] as $k=>$v) {
                $categoryList[$v['id']] = $v;
                if ($v['pid']) {
                    $categoryList[$v['pid']]['son'][$v['id']] = $v;
                }
            }
        }
        if ($categorys['items']) {
            foreach ($categorys['items'] as $ck=>$cv) {
                if ($cv['pid']) {
                    $categoryList[$cv['pid']]['son'][$cv['id']] = $cv;
                }
            }
        }

        $params['category'] = $categoryList;
        return $this->render("boss/category.html");
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
        $pCategorys = $dbCategory->queryAllCategory($categoryCondition);
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
}
