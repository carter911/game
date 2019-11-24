<?php

namespace app\common\logic;


 use app\common\model\Supplier;

 class Pgw{

     static  $gameType = [
         'FFA20PS4','FFA20XBO','FFA20PCC'
     ];
     public  $gateway = "";
     public function __construct($gateway="")
     {

     }

     public function getSupplier(&$param)
     {
        //选择不同的供应商
         $model = new Supplier();
         $list = $model->getList()->toArray();
         $info = [];

         //规则 获取所有满足条件的上游 新的规则
//         $supplier_list = [];
//
//         foreach ($list as $key => $val){
//             if($val['status'][$param['platform']] == 'online'){
//                 if(empty($info)){
//                     $supplier_list[] = $val;
//                 }
//             }
//         }
//
//         $num = count($supplier_list);
//         $current = rand(0,$num-1);
//         $info = $supplier_list[$current];

         foreach ($list as $key => $val){
             if($val['status'][$param['platform']] == 'online' && $val['price'][$param['platform']] !=999){
                 $pgw_price = round($val['price'][$param['platform']]*($param['amount']/1000),2);
                 if(empty($info) && $pgw_price<$param['price']){
                     $info = $val;
                 }else{
                     if($info['price'][$param['platform']]> $val['price'][$param['platform']]){
                         $info = $val;
                     }
                 }

             }
         }

         dump($info);die;
         if($info){
             $param['pgw_id'] = $info['id'];
             $param['pgw_payment'] = $info['pgw'];
             //$param['status'] = $info['status'][$param['platform']];
             if(empty($param['amount'])){
                 $param['amount'] = 1;
             }
             $param['pgw_price'] = round($info['price'][$param['platform']]*($param['amount']/1000),2);
             $param['pgw_info'] = $info;
             if($param['pgw_price']>=$param['price']*0.95){
                 $param['pgw_id'] = 0;
             }
         }
     }

     public function pgw($param)
     {
            //redis
     }

     public function getPrice()
     {

     }

 }
