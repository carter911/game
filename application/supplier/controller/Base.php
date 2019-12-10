<?php
namespace app\supplier\controller;

use think\Controller;
use tp5redis\Redis;

class Base extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $supplier = session('supplier');
        Redis::hSet('request_supplier_log',time(),json_encode(['ip'=>$this->request->ip(),'info'=>$supplier,'request'=>$this->request->param()]));
        //$this->
    }

    public function checkSign()
    {

    }

    public function make()
    {

    }
}
