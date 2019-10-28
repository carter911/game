<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function retData($data= null,$code='200',$message='success'){
    \think\Log::info($data);
    \think\Log::info($message);
    die(json_encode(
        [
            'data'=>$data,
            'code'=>$code,
            'message'=>$message
        ]
    ));
}
