<?php

namespace app\common\logic;


 use app\common\model\Supplier;

 class Pgw{
     public function __construct()
     {

     }

     public function getSupplier(&$param)
     {
        //选择不同的供应商
         $model = new Supplier();
         $list = $model->getList()->toArray();
         $info = [];
         foreach ($list as $key => $val){
             if($val['status'][$param['platform']] == 'online'){
                 if(empty($info)){
                     $info = $val;
                 }else{
                     if($info['price'][$param['platform']]> $val['price'][$param['platform']]){
                         $info = $val;
                     }
                 }

             }
         }
         if($info){
             $param['pgw_id'] = $info['id'];
             $param['status'] = $info['status'][$param['platform']];
             if(empty($param['amount'])){
                 $param['amount'] = 1000;
             }
             $param['pgw_price'] = round($info['price'][$param['platform']]*($param['amount']/1000),2);
             if($param['pgw_price']>=$param['price']){
                 $param['pgw_id'] = 0;
             }
         }



     }

     public function pgw($param)
     {
            //redis
     }

 }
