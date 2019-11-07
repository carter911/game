<?php

namespace app\cron\controller;

class Order
{
    public function index()
    {
        $model = new \app\common\model\Merchant();
        $list = $model->where(['status'=>'online'])->select()->toArray();
        dump($list);die;
    }

}
