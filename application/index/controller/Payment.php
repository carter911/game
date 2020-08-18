<?php
/**
 * Created by PhpStorm.
 * User: Leon4055
 * Date: 2020-06-05
 * Time: 11:57
 */

namespace app\index\controller;

class Payment extends Base
{
    public function index()
    {
        return view("payment/index");
    }

    public function payments()
    {
        return view("payment/payments");
    }
}
