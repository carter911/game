<?php
namespace app\admin\controller;



class Dashboard extends AdminBase
{


    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_order";
        $this->template = "dashboard/index";
    }



}