<?php
namespace app\admin\controller;

use \app\admin\library\VueTable;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class AdminBase extends Controller
{

    public $template = '/common/curd';
    use VueTable;
    public $authIndex = "";

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
        $this->authIndex = strtolower(Request::instance()->module()."/".Request::instance()->controller()."/".Request::instance()->action());
        if(strtolower(Request::instance()->controller()) !='login'){
            $auth_list = Session::get('auth_list');
            $flag = false;
            foreach ($auth_list as $key => $val){
                $auths = explode("/",$val);
                if(
                    ($val == $this->authIndex || $auths[0] =='*') &&
                    ($val == $this->authIndex || $auths[1] =='*') &&
                    ($val == $this->authIndex || $auths[2] =='*')
                ){
                    $flag = true;
                }
            }
            if(!$flag){
               echo '暂无权限';die;
            }
            if(empty($admin_user)){
                if($isAjax){
                    return ['code'=>900,'message'=>'请登录'];
                }
                $this->redirect('/admin/login/index');
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
