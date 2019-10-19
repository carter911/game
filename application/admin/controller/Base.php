<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Base extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $admin_id = session('admin_id');
        $action = Request::instance()->action();
        if(empty($admin_id) && !in_array($action,['login','checklog'])){
            $this->redirect('login');
        }
        $info = Db::name('admin')->find($admin_id);
        $this->assign('admin_info', $info);
        $this->assign('action',$action);
    }

    public function checkSign()
    {

    }

    public function make()
    {

    }
}
