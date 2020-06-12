<?php
namespace app\index\controller;

use app\index\library\Column;
use app\index\library\form\Select;
use app\index\library\Grid;
use app\index\library\VueTable;
use think\Db;
use think\Request;

class Admin extends AdminBase
{
    //引用VueTable 必须要指定对应的表
    use \app\index\library\VueTable;


    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_admin";
    }

    public function tableConfig()
    {
        return Grid::make([],function (Grid $grid  ){
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(12)->setEditDisabled(true)->setLabel('ID')->setSearch(false);
            $grid->column('user_name')
                ->setLabel('用户名称')
                ->setAddDisplay(true)
                ->setSpan(12)
                ->setEditDisabled(false)
                ->setSearch(true);
            $grid->column('password')
                ->setLabel('密码')
                ->setShowColumn(false)
                ->setAddDisplay(true)
                ->setWidth(100)
                ->setType(Column::$FORM_PASSWORD)
                ->setSpan(12)
                ->setEditDisabled(false)
                ->setSearch(false);
            $grid->column('login_time')->setLabel('登陆时间')->setAddDisplay(false)->setWidth(160)->setSpan(12)->setEditDisabled(true)->setSearch(false);
            $grid->column('create_time')->setLabel('创建时间')->setAddDisplay(false)->setWidth(160)->setSpan(12)->setEditDisabled(true)->setSearch(false);
            $grid->column('update_time')->setLabel('更新时间')->setAddDisplay(false)->setWidth(160)->setSpan(12)->setEditDisabled(true)->setSearch(false);
        })->setExpand(false)
            ->setAddBtn(true)
            ->setExcelBtn(false)
            ->setSaveBtnTitle('保存数据')
            ->setPrintBtn(false);
    }

    public function formatList($list)
    {
        return $list;
    }

//    public function reset()
//    {
//        $this->
//    }



    public function paramsUpdate($data,$type)
    {
        if($type == self::$option_save){
            $data[0]['password'] = self::makePassword($data[0]['password']);
            $data[0]['login_time'] = date("Y-m-d H:i:s",time());
            return $data;
        }
        if($type == self::$option_update){
            if(strlen($data[0]['password']) !=32){
                $data[0]['password'] = self::makePassword($data[0]['password']);
            }
           return $data;
        }
        return $data;
    }

    public static function makePassword($string)
    {
        return md5(md5($string).'chenrj123');
    }

}
