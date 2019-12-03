<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use tp5redis\Redis;

class Base extends Controller
{

    public function __construct()
    {
        parent::__construct();

        $admin_id = session('admin_id');
        $action = Request::instance()->action();
        $controller = Request::instance()->controller();
        if(empty($admin_id) && !in_array($action,['login','checklog'])){
            $this->redirect('login');
        }
        $info = Db::name('admin')->find($admin_id);

        $this->assign('admin_info', $info);
        $this->assign('action',$action);
        $this->assign('controller',$controller);
        Redis::hSet('request_log',time(),json_encode(['ip'=>$this->request->ip(),'info'=>$info]));
        $this->assign('path',strtolower($controller).'/'.strtolower($action));
    }

    public function checkSign()
    {

    }

    public function make()
    {

    }
}
