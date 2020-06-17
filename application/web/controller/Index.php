<?php

namespace app\web\controller;



use think\Db;
use think\Request;

class Index extends Base
{
    public function index()
    {

        return view('index');
    }

    public function cart_info()
    {
        return view('index/cart_info');
    }

    public function product(Request $request)
    {
        //$category = $request->get('category',0);
        $category = $request->param('category');
        $category = Db::name('f_category')->where(['category_name'=>$category])->field('id,category_name')->find();
        if(empty($category['id'])){
            $this->error('page is not find');
        }
        $list = Db::name('f_product')->where(['category_id'=>$category['id']])->select();
        foreach ($list as $key =>&$val){
            $val['amount_bag'] = explode(",",$val['amount_bag']);
        }
        $this->assign('list',$list);
        $this->assign('category',$category);
        return view('index/detail');
    }
}
