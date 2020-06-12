<?php
namespace app\index\library\form;

trait Element{

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


    public $label="";
    public $value="";
    public $type="";

    public function __construct()
    {
        $this->setType(self::$FORM_INPUT);
    }
    /**
     * @param string $label
     * @return Select
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param string $value
     * @return Select
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return Select
     */
    public function setType($value)
    {
        $this->value = $value;
        return $this;
    }

    public static function make(...$params)
    {
        return new static(...$params);
    }


}
