<?php

namespace app\index\controller;
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
        $this->assign('title','Buy FIFA 20 Coins - Cheap and Safe FUT 20 Coins For Sale -FUT4GAME ');
        $this->assign('keyword','FIFA 20 Coins,FUT 20 Coins,FIFA 20 Ultimate Team Coins');
        $this->assign('desc','Buy Cheap and Safe FIFA 20 Coins to grow Ultimate Team!IGVault is reliable place to buy FUT 20 Coins with fast,safe payment and 24/7 online support.');
        $this->assign('user_info',session('user_info'));
    }
}
