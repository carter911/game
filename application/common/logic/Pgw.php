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

         //规则 获取所有满足条件的上游
         $supplier_list = [];
         foreach ($list as $key => $val){
             if($val['status'][$param['platform']] == 'online'){
                 if(empty($info)){
                     $supplier_list[] = $val;
                 }
             }
         }
         foreach ($list as $key => $val){
             if($val['status'][$param['platform']] == 'online'){
                 $pgw_price = round($val['price'][$param['platform']]*($param['amount']),2);
                 if($pgw_price<$param['price']*0.95){
                     if(isset($info)){
                         $old_price = round($info['price'][$param['platform']]*($param['amount']),2);
                         if($pgw_price<=$old_price){
                             $info = $val;
                         }
                     }else{
                         $info = $val;
                     }
                 }
             }
         }
         if($info){
             $param['pgw_id'] = $info['id'];
             $param['pgw_payment'] = $info['pgw'];
             //$param['status'] = $info['status'][$param['platform']];
             if(empty($param['amount'])){
                 $param['amount'] = 1;
             }
             $param['pgw_price'] = round($info['price'][$param['platform']]*($param['amount']),2);
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
