<?php
/**
 * Created by PhpStorm.
 * User: chenrj
 * Date: 2020-06-07
 * Time: 10:40
 */

namespace app\index\library;


use app\index\library\form\Element;
use think\Db;
use think\Exception;
use think\Request;

class Column
{
    use Element;
    public static $FORM_INPUT='input';
    public static $FORM_SELECT='select';
    public static $FORM_TEXTAREA='textarea';
    public static $FORM_ARRAY='array';
    public static $FORM_COLOR='color';
    public static $FORM_CASCADER='cascader';
    public static $FORM_CHECKBOX='checkbox';
    public static $FORM_DATE='date';
    public static $FORM_DATETIME='datetime';
    public static $FORM_DATERANGE='daterange';
    public static $FORM_DATETIMERANGE='datetimerange';
    public static $FORM_DATES='dates';
    public static $FORM_DYNAMIC='dynamic';
    public static $FORM_ICON='icon';
    public static $FORM_IMG='img';
    public static $FORM_MONTH='month';
    public static $FORM_PASSWORD='password';
    public static $FORM_RADIO='radio';
    public static $FORM_SWITCH='switch';
    public static $FORM_SLIDER='slider';
    public static $FORM_RATE='rate';
    public static $FORM_TIME='time';
    public static $FORM_TIMERANGE='timerange';
    public static $FORM_TREE='tree';
    public static $FORM_URL='url';
    public static $FORM_WEEK='week';
    public static $FORM_YEAR='week';
    public static $FORM_UPLOAD='upload';
    public static $FORM_UEDITOR='ueditor';

//    public $accept="";
//    public $addDisabled=false;
//    public $addDisplay= true;
//    public $align='left';
//    public $append="";
//    public $cell=false;
//    public $clearable=false;
//    public $disabled=false;
//    public $editDisabled=false;
//    public $editDisplay=true;
//    public $endPlaceholder="";
//    public $filesize="";
//    public $filterMultiple=true;
//    public $filters="";
//    public $filterMethod="";
//    public $fixed=false;
//    public $filterable=false;
//    public $formatter="";
//    public $format="";
//    public $formslot=false;
//    public $typeslot=false;
//    public $formWidth="50%";
//    public $hide=true;
    public $label="";
//    public $maxRows=4;
//    public $minRows=2;
//    public $minWidth="auto";
//    public $multiple="false";
//    public $overHidden="false";
//    public $placeholder="";
//    public $prepend="";
//    public $precision=2;
    public $prop="";
//    public $readonly=false;
//    public $rules="";
//    public $size="medium";
    public $dicData="";
    public $showColumn=true;
//    public $sortable=false;
    public $slot=false;
//    public $span=12;
//    public $gutter=20;
//    public $startPlaceholder="";
    public $type="";
//    public $valueFormat="";
//    public $display=true;
//    public $viewDisplay=true;
//    public $tags=false;
//    public $width='auto';
//    public $value="";






    /**
     * @param string $component
     * @return Column
     */
    public function setComponent($component)
    {
        $this->component = $component;
        return $this;
    }
    /**
     * @param string $dicData
     * @return Column
     */
    public function setDicData($dicData)
    {
        $this->dicData = $dicData;
        return $this;
    }

    public $search = false;

    /**
     * @param bool $search
     * @return Column
     */
    public function setSearch($search)
    {
        $this->search = $search;
        return $this;
    }

    /**
     * @param string $accept
     * @return Column
     */
    public function setAccept($accept)
    {
        $this->accept = $accept;
        return $this;
    }

    /**
     * @param bool $addDisabled
     * @return Column
     */
    public function setAddDisabled($addDisabled)
    {
        $this->addDisabled = $addDisabled;
        return $this;
    }

    /**
     * @param bool $addDisplay
     * @return Column
     */
    public function setAddDisplay($addDisplay)
    {
        $this->addDisplay = $addDisplay;
        return $this;
    }

    /**
     * @param string $align
     * @return Column
     */
    public function setAlign($align)
    {
        $this->align = $align;
        return $this;
    }

    /**
     * @param string $append
     * @return Column
     */
    public function setAppend($append)
    {
        $this->append = $append;
        return $this;
    }

    /**
     * @param bool $cell
     * @return Column
     */
    public function setCell($cell)
    {
        $this->cell = $cell;
        return $this;
    }

    /**
     * @param bool $clearable
     * @return Column
     */
    public function setClearable($clearable)
    {
        $this->clearable = $clearable;
        return $this;
    }

    /**
     * @param bool $disabled
     * @return Column
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @param bool $editDisabled
     * @return Column
     */
    public function setEditDisabled($editDisabled)
    {
        $this->editDisabled = $editDisabled;
        return $this;
    }

    /**
     * @param bool $editDisplay
     * @return Column
     */
    public function setEditDisplay($editDisplay)
    {
        $this->editDisplay = $editDisplay;
        return $this;
    }

    /**
     * @param string $endPlaceholder
     * @return Column
     */
    public function setEndPlaceholder($endPlaceholder)
    {
        $this->endPlaceholder = $endPlaceholder;
        return $this;
    }

    /**
     * @param string $filesize
     * @return Column
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;
        return $this;
    }

    /**
     * @param bool $filterMultiple
     * @return Column
     */
    public function setFilterMultiple($filterMultiple)
    {
        $this->filterMultiple = $filterMultiple;
        return $this;
    }

    /**
     * @param string $filters
     * @return Column
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * @param string $filterMethod
     * @return Column
     */
    public function setFilterMethod($filterMethod)
    {
        $this->filterMethod = $filterMethod;
        return $this;
    }

    /**
     * @param bool $fixed
     * @return Column
     */
    public function setFixed($fixed)
    {
        $this->fixed = $fixed;
        return $this;
    }

    /**
     * @param bool $filterable
     * @return Column
     */
    public function setFilterable($filterable)
    {
        $this->filterable = $filterable;
        return $this;
    }

    /**
     * @param string $formatter
     * @return Column
     */
    public function setFormatter($formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @param string $format
     * @return Column
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param bool $formslot
     * @return Column
     */
    public function setFormslot($formslot)
    {
        $this->formslot = $formslot;
        return $this;
    }

    /**
     * @param bool $typeslot
     * @return Column
     */
    public function setTypeslot($typeslot)
    {
        $this->typeslot = $typeslot;
        return $this;
    }

    /**
     * @param string $formWidth
     * @return Column
     */
    public function setFormWidth($formWidth)
    {
        $this->formWidth = $formWidth;
        return $this;
    }

    /**
     * @param bool $hide
     * @return Column
     */
    public function setHide($hide)
    {
        $this->hide = $hide;
        return $this;
    }

    /**
     * @param string $label
     * @return Column
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param int $maxRows
     * @return Column
     */
    public function setMaxRows($maxRows)
    {
        $this->maxRows = $maxRows;
        return $this;
    }

    /**
     * @param int $minRows
     * @return Column
     */
    public function setMinRows($minRows)
    {
        $this->minRows = $minRows;
        return $this;
    }

    /**
     * @param string $minWidth
     * @return Column
     */
    public function setMinWidth($minWidth)
    {
        $this->minWidth = $minWidth;
        return $this;
    }

    /**
     * @param string $multiple
     * @return Column
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * @param string $overHidden
     * @return Column
     */
    public function setOverHidden($overHidden)
    {
        $this->overHidden = $overHidden;
        return $this;
    }

    /**
     * @param string $placeholder
     * @return Column
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @param string $prepend
     * @return Column
     */
    public function setPrepend($prepend)
    {
        $this->prepend = $prepend;
        return $this;
    }

    /**
     * @param int $precision
     * @return Column
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
        return $this;
    }

    /**
     * @param string $prop
     * @return Column
     */
    public function setProp($prop)
    {
        $this->prop = $prop;
        return $this;
    }

    /**
     * @param bool $readonly
     * @return Column
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * @param string $rules
     * @return Column
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * @param string $size
     * @return Column
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @param bool $showColumn
     * @return Column
     */
    public function setShowColumn($showColumn)
    {
        $this->showColumn = $showColumn;
        return $this;
    }

    /**
     * @param bool $sortable
     * @return Column
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * @param bool $slot
     * @return Column
     */
    public function setSlot($slot)
    {
        $this->slot = $slot;
        return $this;
    }

    /**
     * @param bool $slot
     * @return Column
     */
    public function setHtml($html)
    {
        $this->slot = true;
        $this->html = $html;
        return $this;
    }

    /**
     * @param int $span
     * @return Column
     */
    public function setSpan($span)
    {
        $this->span = $span;
        return $this;
    }

    /**
     * @param int $gutter
     * @return Column
     */
    public function setGutter($gutter)
    {
        $this->gutter = $gutter;
        return $this;
    }

    /**
     * @param string $startPlaceholder
     * @return Column
     */
    public function setStartPlaceholder($startPlaceholder)
    {
        $this->startPlaceholder = $startPlaceholder;
        return $this;
    }

    /**
     * @param string $type
     * @return Column
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param string $valueFormat
     * @return Column
     */
    public function setValueFormat($valueFormat)
    {
        $this->valueFormat = $valueFormat;
        return $this;
    }

    /**
     * @param bool $display
     * @return Column
     */
    public function setDisplay($display)
    {
        $this->display = $display;
        return $this;
    }

    /**
     * @param bool $viewDisplay
     * @return Column
     */
    public function setViewDisplay($viewDisplay)
    {
        $this->viewDisplay = $viewDisplay;
        return $this;
    }

    /**
     * @param bool $tags
     * @return Column
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @param string $width
     * @return Column
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    /**
     * @param string $value
     * @return Column
     */
    public function setPropsHttp($value)
    {
        $this->propsHttp = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setTip($value)
    {
        $this->tip = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setAction($value)
    {
        $this->action = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Column
     */
    public function setListType($value)
    {
        $this->listType = $value;
        return $this;
    }


}
