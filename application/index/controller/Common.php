<?php
namespace app\index\controller;

use app\common\model\Order;
use app\index\library\Column;
use app\index\library\Grid;
use think\Controller;
use think\Request;

class Common extends Controller
{

    public function upload(Request $request)
    {
        $file = request()->file('file');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                retData(['url'=>$request->domain().'/uploads/'.$info->getSaveName()]);
            }else{
                retData([],500,$file->getError());
            }
        }else{
            retData([],404,'图片不存在');
        }

    }
}
