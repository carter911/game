<?php
namespace app\index\controller;

use app\index\library\VueTable;
use think\Controller;
use think\Db;
use think\Request;

class AdminBase extends Controller
{

    public $template = '/common/curd';
    use VueTable;

    public function __construct()
    {
        parent::__construct();
        $admin_user = session('admin_user');
        $this->requestLog();
        $baseUrl = strtolower('/'.Request::instance()->module()."/".Request::instance()->controller()."/");
        $this->tableName = strtolower("f_".Request::instance()->controller());
        $this->indexUrl= $baseUrl."get_list";
        $this->updateUrl= $baseUrl."store";
        $this->addUrl= $baseUrl."save";
        $this->delUrl= $baseUrl."del";
        $this->router= $baseUrl."index";
        $isAjax = Request::instance()->isAjax();

        if(strtolower(Request::instance()->controller()) !='login'){
            if(empty($admin_user)){
                if($isAjax){
                    return ['code'=>900,'message'=>'请登录'];
                }
                $this->redirect('/index/login/index');
            }
        }

    }

    public function requestLog()
    {
        $admin_user = session('admin_user');
        $id = isset($admin_user['id'])?$admin_user['id']:0;
        $url =  Request::instance()->url();
        $params = json_encode(Request::instance()->param());
        $ip = Request::instance()->ip();
        Db::name('f_admin_log')->insert(['admin_id'=>$id,'url'=>$url,'params'=>$params,'ip'=>$ip,'create_time'=>date("Y-m-d H:i:s")]);

    }

}
