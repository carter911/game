<?php
namespace app\index\controller;

use app\index\library\Column;
use app\index\library\form\Element;
use app\index\library\form\Select;
use app\index\library\Grid;
use think\Db;
use think\Request;

class Product extends AdminBase
{
    public static $currency = [
        'EUR','USD','CNY'
    ];

    public static  $order_status = [
        1=>'未付款',2=>'已付款',3=>'已发货',4=>'发货失败',5=>'已完成',6=>'退款中',7=>'退款完成',
    ];

    //引用VueTable 必须要指定对应的表
    use \app\index\library\VueTable;


    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_product";
//        $this->relationTable = [
//            [
//                'merchant m',
//                'm.id = order.merchant_id',
//                'left',
//                'm.name'
//            ]
//        ];
    }

    public function tableConfig()
    {

        return Grid::make([],function (Grid $grid  ){
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(12)->setEditDisabled(true)->setLabel('ID')->setSearch(false);
            $grid->column('title')->setLabel('标题')
                ->setAddDisplay(true)
                ->setWidth(100)
                ->setSpan(12)
                ->setEditDisabled(false)
                ->setSearch(true);
            $grid->column('category_id')
                ->setLabel('分类')
                ->setAddDisplay(true)
                ->setWidth(100)
                ->setSpan(12)
                ->setEditDisabled(false)
                ->setType(Column::$FORM_TREE)
                ->setDicData(self::getCategory())
                ->setSearch(true);
            $grid->column('price')->setLabel('单价')->setWidth(100)->setShowColumn(true)->setSpan(12);
            $grid->column('currency')
                ->setLabel('币种')->setAddDisplay(true)
                ->setType(Column::$FORM_SELECT)
                ->setDicData(self::getCurrency())
                ->setWidth(100)->setSpan(12)->setEditDisabled(false)->setSearch(false);
            $grid->column('keyword')->setLabel('关键字')->setOverHidden(true)->setWidth(160)->setSpan(24)->setEditDisabled(false)->setSearch(false);
            $grid->column('desc')->setLabel('简介')->setOverHidden(true)->setType(Column::$FORM_TEXTAREA)->setAddDisplay(true)->setSpan(24)->setEditDisabled(false)->setSearch(false);
            $grid->column('image')->setLabel('图片')->setType(Column::$FORM_UPLOAD)->setAddDisplay(true)->setWidth(120)->setSpan(12)->setEditDisabled(false)->setSearch(false)
                ->setAction('http://www.game.test/index/common/upload')->setSpan(24)->setListType('picture-img')->setPropsHttp(["home"=>'','res'=>'data']);
            $grid->column('amount_bag')->setLabel('金币包')->setWidth(100)->setSpan(24)->setEditDisabled(false)->setSearch(false);
//            $grid->column('ext_json')->setLabel('扩展字段')
//                ->setType(Column::$FORM_TEXTAREA)
//                ->setDicData(self::getOrderStatus())
//                ->setAddDisplay(true)->setWidth(100)->setSpan(24)->setEditDisabled(false)->setSearch(false);
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
        $list = Db::table('f_category')->field('id as value,category_name as label,parent_id,id')->order('parent_id asc')->select();
        //children
        $data = [];
        foreach ($list as $key => $val){
            if($val['parent_id'] == 0){
                $data[$val['id']] = $val;
            }else{
                $data[$val['parent_id']]['children'][] = $val;
            }
        }
        $data = array_values($data);
        return $data;
    }

}
