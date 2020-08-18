<?php
/**
 * Created by PhpStorm.
 * User: chenrj
 * Date: 2020-06-07
 * Time: 10:40
 */

namespace app\admin\library;


use think\Db;
use think\Exception;
use think\Request;

class From
{

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

    public $dicData="";

    public function __construct()
    {
        $this->type=self::$FORM_INPUT;
    }



}
