<?php
namespace app\index\controller;

use app\common\model\Order;
use app\index\library\Column;
use app\index\library\Grid;
use think\Request;

class Index extends Base
{
    //引用VueTable 必须要指定对应的表
    use \app\index\library\VueTable;

    public function __construct()
    {
        parent::__construct();
        $this->table = "order";
        $this->relationTable = [
            [
                'merchant m',
                'm.id = order.merchant_id',
                'left',
                'm.name'
            ]
        ];
    }

    public function tableConfig()
    {
        $table = Grid::make(new Order(),function (Grid $grid  ){
            $grid->column('image')->setLabel('id')->setSearch(true);
            $grid->column('id')->setReadonly(true)->setLabel('id')->setSearch(true);
            $grid->column('name')->setLabel('名称')->setSearch(true);
            $grid->column('name')->setLabel('啊哈哈');
            $grid->column('name')->setLabel('666')->setType('textarea');
            $grid->column('name')->setLabel('666')->setSearch(true)->setType('date');
            $grid->column('name');
        })->setTitle('哈哈哈哈哈哈哈哈')
            ->setAddBtn(true)->setExcelBtn(false)->setPrintBtn(false);
        $grid = json_encode($table,true);
        return $grid;
    }

}