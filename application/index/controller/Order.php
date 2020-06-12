<?php
namespace app\index\controller;

use app\index\library\Column;
use app\index\library\form\Select;
use app\index\library\From;
use app\index\library\Grid;

class Order extends AdminBase
{
    public static $currency = [
        'EUR','USD','CNY'
    ];

    public static  $order_status = [
        1=>'未付款',2=>'已付款',3=>'已发货',4=>'发货失败',5=>'已完成',6=>'退款中',7=>'退款完成',
    ];
    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_order";
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
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(24)->setEditDisabled(true)->setLabel('id')->setSearch(false);
            $grid->column('order_no')->setLabel('订单号')->setAddDisplay(true)->setWidth(100)->setSpan(24)->setEditDisabled(true)->setSearch(true);
            $grid->column('product_id')->setLabel('产品id')->setShowColumn(false);
            $grid->column('product_name')->setLabel('产品名称')->setAddDisplay(true)->setWidth(120)->setSpan(24)->setEditDisabled(true)->setSearch(true);
            $grid->column('number')->setLabel('数量')->setAddDisplay(true)->setWidth(100)->setSpan(24)->setEditDisabled(true)->setSearch(false);
            $grid->column('user_id')->setLabel('用户id')->setWidth(100)->setSpan(24)->setShowColumn(false)->setEditDisabled(true)->setSearch(false);
            $grid->column('user_email')->setLabel('用户邮箱')->setAddDisplay(true)->setWidth(160)->setSpan(24)->setEditDisabled(true)->setSearch(true);
            $grid->column('price')->setLabel('价格')->setAddDisplay(true)->setWidth(100)->setSpan(12)->setEditDisabled(true)->setSearch(false);
            $grid->column('currency')
                ->setLabel('币种')
                ->setAddDisplay(true)
                ->setWidth(80)
                ->setSpan(12)
                ->setType(Column::$FORM_SELECT)
                ->setDicData(self::getCurrency())
                ->setEditDisabled(true)
                ->setSearch(false);
            $grid->column('create_time')->setLabel('创建时间')->setAddDisplay(false)->setWidth(160)->setSpan(24)->setEditDisabled(true)->setSearch(false);
            $grid->column('update_time')->setLabel('更新时间')->setAddDisplay(false)->setWidth(160)->setSpan(24)->setEditDisabled(true)->setSearch(false);
            $grid->column('status')->setLabel('状态')
                ->setType(Column::$FORM_SELECT)
                ->setDicData(self::getOrderStatus())
                ->setWidth(100)->setSpan(24)->setEditDisabled(true)->setSearch(true);

            $grid->column('pay_data')->setLabel('支付数据')->setShowColumn(false)->setAddDisplay(true)->setWidth(100)->setSpan(24)->setEditDisabled(true)->setSearch(false);
            $grid->column('pay_time')->setLabel('支付时间')->setValue('1111')->setAddDisplay(false)->setWidth(100)->setSpan(24)->setEditDisabled(true)->setSearch(false);
            $grid->column('reason')->setLabel('备注')
                ->setType(Column::$FORM_TEXTAREA)
                ->setAddDisplay(true)->setSpan(24)->setOverHidden(true)->setEditDisabled(true)->setSearch(false);
        })->setExpand(false)
            ->setViewBtn(false)
            ->setAddBtn(false)
            ->setExcelBtn(false)
            ->setSaveBtnTitle('保存数据')
            ->setPrintBtn(false);
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
        $list[] = Select::make()->setValue('')->setLabel('全部');
        foreach (self::$order_status as $key =>$val){
            $list[] = Select::make()->setValue($key)->setLabel($val);
        }
        return $list;
    }

}
