<?php
namespace app\index\controller;

use app\index\library\VueTable;
use think\Controller;
use think\Request;

class AdminBase extends Controller
{

    public $template = '/common/curd';
    use VueTable;

    public function __construct()
    {
        parent::__construct();
        $baseUrl = strtolower('/'.Request::instance()->module()."/".Request::instance()->controller()."/");
        $this->tableName = strtolower("f_".Request::instance()->controller());
        $this->indexUrl= $baseUrl."get_list";
        $this->updateUrl= $baseUrl."store";
        $this->addUrl= $baseUrl."save";
        $this->delUrl= $baseUrl."del";
        $this->router= $baseUrl."index";
        $isAjax = Request::instance()->isAjax();
        $admin_user = session('admin_user');
        if(strtolower(Request::instance()->controller()) !='login'){
            if(empty($admin_user)){
                if($isAjax){
                    return ['code'=>900,'message'=>'请登录'];
                }
                $this->redirect('/index/login/index');
            }
        }

    }

}
