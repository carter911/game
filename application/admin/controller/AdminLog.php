<?php
namespace app\admin\controller;

use \app\admin\library\Column;
use \app\admin\library\form\Select;
use \app\admin\library\Grid;
use \app\admin\library\VueTable;
use think\Db;
use think\Request;

class AdminLog extends AdminBase
{
    //引用VueTable 必须要指定对应的表
    use \app\admin\library\VueTable;


    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_admin_log";
    }

    public function tableConfig()
    {
        return Grid::make([],function (Grid $grid  ){
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(12)->setEditDisabled(true)->setLabel('ID')->setSearch(false);
            $grid->column('admin_id')
                ->setLabel('用户名称')
                ->setAddDisplay(true)
                ->setSpan(12)
                ->setWidth(160)
                ->setEditDisabled(false)
                ->setSearch(true);
            $grid->column('url')
                ->setLabel('请求地址')
                ->setAddDisplay(true)
                ->setSpan(12)
                ->setEditDisabled(false)
                ->setSearch(true);
            $grid->column('create_time')->setLabel('创建时间')->setWidth(160)->setAddDisplay(false)->setSpan(12)->setEditDisabled(true)->setSearch(false);
        })->setExpand(true)
            ->setAddBtn(false)
            ->setExcelBtn(false)
            ->setEditBtn(false)
            ->setViewBtn(false)
            ->setDelBtn(false)
            ->setSaveBtnTitle('保存数据')
            ->setPrintBtn(false);
    }

}
