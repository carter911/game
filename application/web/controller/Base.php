<?php

namespace app\web\controller;
use think\Controller;
use think\Db;

class Base extends Controller
{
    public function _initialize()
    {
//        $user_info =session_name('user_info');

        //banner
        $banner = Db::name("f_banner")->select();

        //分类
        $category = Db::name("f_category")->order('parent_id asc')->select();
        $menu = [];
        foreach ($category as $key => $val){
            if($val['parent_id'] == 0){
                $menu[$val['id']] = $val;
            }else{
                $menu[$val['parent_id']]['children'][] = $val;
            }
        }

        $menu = array_values($menu);
        $this->assign('banner',$banner);
        $this->assign('menu',$menu);
        $this->assign('user_info',session('user_info'));
    }
}
