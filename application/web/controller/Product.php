<?php

namespace app\web\controller;

use app\web\controller\Base;

class Product extends Base
{
    public function index()
    {
        return view('index');
    }

    public function cart_info()
    {
        return view('index/cart_info');
    }

    public function detail()
    {
        return view('index/detail');
    }
}
