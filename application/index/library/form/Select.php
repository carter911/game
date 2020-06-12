<?php
namespace app\index\library\form;

class Select {

    use Element;

    public function __construct()
    {
        $this->setType(self::$FORM_SELECT);
    }



}
