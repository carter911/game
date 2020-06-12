<?php
namespace app\index\controller;

use app\index\library\Column;
use app\index\library\form\Select;
use app\index\library\Grid;
use app\index\library\VueTable;
use think\Db;
use think\Request;

class User extends AdminBase
{
    //引用VueTable 必须要指定对应的表
    use \app\index\library\VueTable;


    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_user";
    }

    public function tableConfig()
    {
        return Grid::make([],function (Grid $grid  ){
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(12)->setEditDisabled(true)->setLabel('ID')->setSearch(false);
            $grid->column('username')
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

            $grid->column('email')
                ->setLabel('邮箱')
                ->setShowColumn(true)
                ->setAddDisplay(true)
                ->setSpan(12)
                ->setEditDisabled(false)
                ->setSearch(true);

            $grid->column('phone')
                ->setLabel('电话')
                ->setShowColumn(true)
                ->setAddDisplay(true)
                ->setWidth(150)
                ->setSpan(12)
                ->setEditDisabled(false)
                ->setSearch(false);
            $grid->column('country')
                ->setLabel('国家')
                ->setShowColumn(true)
                ->setAddDisplay(true)
                ->setWidth(150)
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
//        foreach ($list as $key => $val){
//            $val['amount_bag'] = "";//json_decode($val['amount_bag'],true);
//            $list[$key] = $val;
//        }
        return $list;
    }

    public static function getCurrency()
    {

        $list = [];
        foreach (self::$currency as $key =>$val){
            $list[] = Select::make()->setValue($val)->setLabel($val);
        }
        return $list;
    }

    public static function getOrderStatus()
    {
        $list = [];
        foreach (self::$order_status as $key =>$val){
            $list[] = Select::make()->setValue($val)->setLabel($val);
        }
        return $list;
    }

    public static function getCategory()
    {
        $list = Db::table('f_category')
            ->field('id as value,category_name as label,parent_id,id')
            ->where('parent_id',0)
            ->select();
        //children
        $data = [
            ['label'=>'无父级','value'=>0]
        ];
        foreach ($list as $key => $val){
            if($val['parent_id'] == 0){
                $data[$val['id']] = $val;
            }else{
                $data[$val['parent_id']][] = $val;
            }
        }

        $data = array_values($data);
        return $data;
    }

    public function paramsUpdate($data,$type)
    {
        if($type == self::$option_save || $type == self::$option_update){
            dump($data);
        }
        //dump($data);die;
        return $data;
    }

}
