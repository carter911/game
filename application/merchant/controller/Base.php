<?php
namespace app\merchant\controller;

use think\Controller;
use think\Db;
use think\Request;

class Base extends Controller
{

    protected $merchant_id = 0;
    public function __construct()
    {
        parent::__construct();
        //$this->

        $admin_id = session('merchant_id');
        $action = Request::instance()->action();
        if(empty($admin_id) && !in_array($action,['login','checklog'])){
            $this->redirect('login');
        }
        $info = session('merchant');
        $this->merchant_id = $info['id'];
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
