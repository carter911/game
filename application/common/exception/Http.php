<?php
namespace app\common\exception;

use Exception;
use think\exception\Handle;
class Http extends Handle
{

    public function render(Exception $e)
    {
        return retData(null,$e->getMessage(),'500');
    }

}
