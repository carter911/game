<?php
/**
 * Created by PhpStorm.
 * User: Leon4055
 * Date: 2020-06-07
 * Time: 11:28
 */

namespace app\web\controller;

use app\web\controller\Base;

class Order extends Base
{
    public function index()
    {
        return view("index");
    }
}
