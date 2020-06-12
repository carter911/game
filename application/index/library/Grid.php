<?php
/**
 * Created by PhpStorm.
 * User: chenrj
 * Date: 2020-06-07
 * Time: 10:40
 */

namespace app\index\library;


use app\common\model\Order;
use think\console\command\make\Model;
use think\Db;
use think\Exception;
use think\Request;

class Grid
{

    public $rowKey="";
    public $tip="";
    public $index="";
    public $selection=true;
    public $border="";
    public $expand=true;
    public $headerAlign="";
    public $align="";
    public $menu="";
    public $menuWidth="";
    public $menuAlign="";
    public $menuType="";
    public $dialogWidth="";
    public $dialogHeight="";
    public $dialogType="";
    public $dialogTop="";
    public $dialogFullscreen=false;
    public $dialogEscape=true;
    public $dialogModal=true;
    public $dialogClickModal=true;
    public $dialogCloseBtn=true;
    public $addBtn=true;
    public $editBtn=true;
    public $viewBtn=true;
    public $delBtn=true;
    public $saveBtn=true;
    public $addTitle='新增';
    public $saveBtnTitle="保存";
    public $updateBtn=true;
    public $updateBtnTitle="保存";
    public $cancelBtn=true;
    public $cancelBtnTitle="取消";
    public $searchBtn=true;
    public $refreshBtn=true;
    public $filterBtn=true;
    public $printBtn=true;
    public $excelBtn=true;
    public $columnBtn=true;
    public $title='';
    public $column = [];
    protected $builder = false;

    public function __construct($repository = null, ?\Closure $builder = null)
    {
        call_user_func($builder, $this);
    }


    /**
     * @param string $rowKey
     * @return Grid
     */
    public function setRowKey($rowKey)
    {
        $this->rowKey = $rowKey;
        return $this;
    }

    /**
     * @param string $tip
     * @return Grid
     */
    public function setTip($tip)
    {
        $this->tip = $tip;
        return $this;
    }

    /**
     * @param string $index
     * @return Grid
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @param bool $selection
     * @return Grid
     */
    public function setSelection($selection)
    {
        $this->selection = $selection;
        return $this;
    }

    /**
     * @param string $border
     * @return Grid
     */
    public function setBorder($border)
    {
        $this->border = $border;
        return $this;
    }

    /**
     * @param bool $expand
     * @return Grid
     */
    public function setExpand($expand)
    {
        $this->expand = $expand;
        return $this;
    }

    /**
     * @param string $headerAlign
     * @return Grid
     */
    public function setHeaderAlign($headerAlign)
    {
        $this->headerAlign = $headerAlign;
        return $this;
    }

    /**
     * @param string $align
     * @return Grid
     */
    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * @param string $menu
     * @return Grid
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
        return $this;
    }

    /**
     * @param string $menuWidth
     * @return Grid
     */
    public function setMenuWidth($menuWidth)
    {
        $this->menuWidth = $menuWidth;
        return $this;
    }

    /**
     * @param string $menuAlign
     * @return Grid
     */
    public function setMenuAlign($menuAlign)
    {
        $this->menuAlign = $menuAlign;
        return $this;
    }

    /**
     * @param string $menuType
     * @return Grid
     */
    public function setMenuType($menuType)
    {
        $this->menuType = $menuType;
        return $this;
    }

    /**
     * @param string $dialogWidth
     * @return Grid
     */
    public function setDialogWidth($dialogWidth)
    {
        $this->dialogWidth = $dialogWidth;
        return $this;
    }

    /**
     * @param string $dialogHeight
     * @return Grid
     */
    public function setDialogHeight($dialogHeight)
    {
        $this->dialogHeight = $dialogHeight;
        return $this;
    }

    /**
     * @param string $dialogType
     * @return Grid
     */
    public function setDialogType($dialogType)
    {
        $this->dialogType = $dialogType;
        return $this;
    }

    /**
     * @param string $dialogTop
     * @return Grid
     */
    public function setDialogTop($dialogTop)
    {
        $this->dialogTop = $dialogTop;
        return $this;
    }

    /**
     * @param bool $dialogFullscreen
     * @return Grid
     */
    public function setDialogFullscreen($dialogFullscreen)
    {
        $this->dialogFullscreen = $dialogFullscreen;
        return $this;
    }

    /**
     * @param bool $dialogEscape
     * @return Grid
     */
    public function setDialogEscape($dialogEscape)
    {
        $this->dialogEscape = $dialogEscape;
        return $this;
    }

    /**
     * @param bool $dialogModal
     * @return Grid
     */
    public function setDialogModal($dialogModal)
    {
        $this->dialogModal = $dialogModal;
        return $this;
    }

    /**
     * @param bool $dialogClickModal
     * @return Grid
     */
    public function setDialogClickModal($dialogClickModal)
    {
        $this->dialogClickModal = $dialogClickModal;
        return $this;
    }

    /**
     * @param bool $dialogCloseBtn
     * @return Grid
     */
    public function setDialogCloseBtn($dialogCloseBtn)
    {
        $this->dialogCloseBtn = $dialogCloseBtn;
        return $this;
    }

    /**
     * @param bool $addBtn
     * @return Grid
     */
    public function setAddBtn($addBtn)
    {
        $this->addBtn = $addBtn;
        return $this;
    }

    /**
     * @param bool $editBtn
     * @return Grid
     */
    public function setEditBtn($editBtn)
    {
        $this->editBtn = $editBtn;
        return $this;
    }

    /**
     * @param bool $viewBtn
     * @return Grid
     */
    public function setViewBtn($viewBtn)
    {
        $this->viewBtn = $viewBtn;
        return $this;
    }

    /**
     * @param bool $delBtn
     * @return Grid
     */
    public function setDelBtn($delBtn)
    {
        $this->delBtn = $delBtn;
        return $this;
    }

    /**
     * @param bool $saveBtn
     * @return Grid
     */
    public function setSaveBtn($saveBtn)
    {
        $this->saveBtn = $saveBtn;
        return $this;
    }

    /**
     * @param string $saveBtnTitle
     * @return Grid
     */
    public function setSaveBtnTitle($saveBtnTitle)
    {
        $this->saveBtnTitle = $saveBtnTitle;
        return $this;
    }

    /**
     * @param bool $updateBtn
     * @return Grid
     */
    public function setUpdateBtn($updateBtn)
    {
        $this->updateBtn = $updateBtn;
        return $this;
    }

    /**
     * @param string $updateBtnTitle
     * @return Grid
     */
    public function setUpdateBtnTitle($updateBtnTitle)
    {
        $this->updateBtnTitle = $updateBtnTitle;
        return $this;
    }

    /**
     * @param bool $cancelBtn
     * @return Grid
     */
    public function setCancelBtn($cancelBtn)
    {
        $this->cancelBtn = $cancelBtn;
        return $this;
    }

    /**
     * @param string $cancelBtnTitle
     * @return Grid
     */
    public function setCancelBtnTitle($cancelBtnTitle)
    {
        $this->cancelBtnTitle = $cancelBtnTitle;
        return $this;
    }

    /**
     * @param bool $searchBtn
     * @return Grid
     */
    public function setSearchBtn($searchBtn)
    {
        $this->searchBtn = $searchBtn;
        return $this;
    }

    /**
     * @param bool $refreshBtn
     * @return Grid
     */
    public function setRefreshBtn($refreshBtn)
    {
        $this->refreshBtn = $refreshBtn;
        return $this;
    }

    /**
     * @param bool $filterBtn
     * @return Grid
     */
    public function setFilterBtn($filterBtn)
    {
        $this->filterBtn = $filterBtn;
        return $this;
    }

    /**
     * @param bool $printBtn
     * @return Grid
     */
    public function setPrintBtn($printBtn)
    {
        $this->printBtn = $printBtn;
        return $this;
    }

    /**
     * @param bool $excelBtn
     * @return Grid
     */
    public function setExcelBtn($excelBtn)
    {
        $this->excelBtn = $excelBtn;
        return $this;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param bool $columnBtn
     * @return Grid
     */
    public function setColumnBtn($columnBtn)
    {
        $this->columnBtn = $columnBtn;
        return $this;
    }

    public function setColumn($column=[])
    {
        $this->column[] = $column;
        return $this;
    }

    public static function make(...$params)
    {
        return new static(...$params);
    }

    public function column($prop,$label="")
    {
        $grid = new Column();
        $grid = $grid->setProp($prop);
        $grid = $grid->setLabel(empty($label)?$prop:$label);
        $this->column[] = $grid;
        return $grid;
    }



}
