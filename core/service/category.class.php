<?php

class core_service_category extends SAbstract
{

    public function __construct($id = 0)
    {
        parent::__construct($id);
    }

    public function getList()
    {
        $mdlCategory = new core_model_wb_category();
        $mdlCategory->selectOne($mdlCategory->tableName(),);
    }
}