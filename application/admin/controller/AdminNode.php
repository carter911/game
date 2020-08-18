<?php
namespace app\admin\controller;

use \app\admin\library\Column;
use \app\admin\library\form\Select;
use \app\admin\library\Grid;
use \app\admin\library\VueTable;
use think\Db;
use think\Request;

class AdminNode extends AdminBase
{
    //引用VueTable 必须要指定对应的表
    use \app\admin\library\VueTable;


    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_admin_node";
    }

    public function tableConfig()
    {
        return Grid::make([],function (Grid $grid  ){
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(12)->setEditDisabled(true)->setLabel('ID')->setSearch(false);
            $grid->column('name')
                ->setLabel('节点名称')
                ->setAddDisplay(true)
                ->setSpan(24)
                ->setEditDisabled(false)
                ->setSearch(true);

            $grid->column('url')
                ->setLabel('节点URL')
                ->setAddDisplay(true)
                ->setSpan(24)
                ->setEditDisabled(false)
                ->setSearch(true);

            $grid->column('create_time')->setLabel('创建时间')->setAddDisplay(false)->setWidth(160)->setSpan(12)->setEditDisabled(true)->setSearch(false);
            $grid->column('update_time')->setLabel('更新时间')->setAddDisplay(false)->setWidth(160)->setSpan(12)->setEditDisabled(true)->setSearch(false);
        })->setExpand(false)
            ->setAddBtn(true)
            ->setExcelBtn(false)
            ->setSaveBtnTitle('保存数据')
            ->setPrintBtn(false);
    }



}
