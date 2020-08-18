<?php

namespace app\index\controller;

use app\index\controller\Base;

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
