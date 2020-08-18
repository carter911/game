<?php
namespace app\admin\library\form;

use think\Config;

class Select {

    use Element;

    public $action = "";
    public $listType = "";
    public $propsHttp = "";

    public function __construct()
    {
        $this->setType(self::$FORM_SELECT);
        $this->action = Config::get();
        //$this->listType =
    }





}
