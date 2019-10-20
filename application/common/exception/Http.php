<?php
namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\Log;

class Http extends Handle
{

    public function render(Exception $e)
    {
//        echo "<div style='text-align: center;font-size: 40px;padding: 200px;font-family: 'Andale Mono')'><div style='color: red'>4 0 4</div><div style='color: #008800'>page not found</div></div>";
//        die;

        Log::error($e->getMessage());
        return retData(null,'system error','500');
    }

}
