<?php
namespace app\index\controller;

use app\common\model\Order;
use app\index\library\Column;
use app\index\library\Grid;
use think\Request;

class Banner extends AdminBase
{
    //引用VueTable 必须要指定对应的表
    use \app\index\library\VueTable;

    public function __construct()
    {
        parent::__construct();
        $this->table = "f_banner";
        $this->indexUrl="/index/banner/get_list";
        $this->updateUrl="/index/banner/store";
        $this->addUrl="/index/banner/save";
        $this->delUrl="/index/banner/del";
        $this->router="/index/banner/index";
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
        $table = Grid::make(new Order(),function (Grid $grid  ){
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(24)->setEditDisabled(true)->setLabel('id')->setSearch(false);
            $grid->column('image')->setSpan(24)->setLabel('图片')->setWidth(100)->setSearch(false)->setType('upload')
                ->setAction('http://www.game.test/index/common/upload')->setSpan(24)->setListType('picture-img')->setPropsHttp(["home"=>'','res'=>'data']);
            $grid->column('url')->setLabel('链接地址')->setSpan(24)->setSearch(false)->setType('url');
            $grid->column('create_time')->setLabel('创建时间')->setSpan(12)->setAddDisplay(false)->setEditDisplay(true);
            $grid->column('update_time')->setLabel('更新时间')->setSpan(12)->setAddDisplay(false)->setEditDisplay(true);
        })->setExpand(false)
            ->setAddBtn(true)
            ->setExcelBtn(false)
            ->setSaveBtnTitle('保存数据')
            ->setPrintBtn(false);
        $grid = json_encode($table,true);
        return $grid;
    }

}
