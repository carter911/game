<?php
namespace app\admin\controller;

use \app\admin\library\Column;
use \app\admin\library\form\Select;
use \app\admin\library\Grid;
use \app\admin\library\VueTable;
use think\Db;
use think\Request;

class Article extends AdminBase
{
    //引用VueTable 必须要指定对应的表
    use \app\admin\library\VueTable;
    public function __construct()
    {
        parent::__construct();
        $this->tableName = "f_article";
    }

    public function tableConfig()
    {
        return Grid::make([],function (Grid $grid  ){
            $grid->column('id')->setAddDisplay(false)->setWidth(100)->setSpan(12)->setLabel('ID')->setSearch(false);
            $grid->column('title')->setLabel('标题')
                ->setAddDisplay(true)
                ->setWidth(200)
                ->setSpan(12)
                ->setOverHidden(true)
                ->setEditDisabled(false)
                ->setSearch(true);
            $grid->column('image')->setLabel('图片')->setType(Column::$FORM_UPLOAD)->setWidth(120)->setSpan(12)->setEditDisabled(false)->setSearch(false)
                ->setAction('http://www.game.test/admin/common/upload')->setSpan(24)->setListType('picture-img')->setPropsHttp(["home"=>'','res'=>'data']);
            $grid->column('intro')->setLabel('简介')->setOverHidden(true)->setType(Column::$FORM_TEXTAREA)->setAddDisplay(true)->setSpan(24)->setEditDisabled(false)->setSearch(false);
            $grid->column('keyword')->setLabel('关键字')->setOverHidden(true)->setType(Column::$FORM_INPUT)->setAddDisplay(true)->setSpan(24)->setEditDisabled(false)->setSearch(false);
            $grid->column('content')->setShowColumn(false)
                ->setLabel('内容')->setOverHidden(true)
                ->setType(Column::$FORM_UEDITOR)
                ->setSpan(24)->setFormslot(true)->setFormHtml('contentForm')
                ->setSearch(false);
            $grid->column('create_time')->setLabel('创建时间')->setAddDisplay(false)->setWidth(160)->setSpan(12)->setEditDisabled(true)->setSearch(false);
            $grid->column('update_time')->setLabel('更新时间')->setAddDisplay(false)->setWidth(160)->setSpan(12)->setEditDisabled(true)->setSearch(false);
        })->setExpand(false)
            ->setDialogWidth(1000)
            ->setDialogModal(false)
            ->setAddBtn(true)
            ->setExcelBtn(false)
            ->setViewBtn(false)
            ->setSaveBtnTitle('保存数据')
            ->setPrintBtn(false);
    }

    public function formatList($list)
    {
        return $list;
    }


}
