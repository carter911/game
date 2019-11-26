<?php

namespace app\common\logic;


 use app\common\model\Supplier;
 use tp5redis\Redis;

 class Pgw{

     /**
      * @param array $gameType
      */
     const RATE=0.95;
     static  $gameType = [
         'FFA20PS4','FFA20XBO','FFA20PCC'
     ];
     public  $gateway = "";
     public function __construct($gateway="")
     {

     }


     public function autoSyncPrice()
     {
         $model = new \app\common\model\Supplier();
         $list = $model->where(['is_auto' => 1])->select()->toArray();
         foreach ($list as $key => $val) {
             $gateway = "app\\common\\gateway\\".$val['pgw'];
             $pgw = new $gateway();
             $price = $pgw->getPrice();
             $status = [];
             foreach ($price as $k => $v){
                 $status[$k] = 'offline';
                 if($v!=999 && $v>0){
                     $status[$k] = 'online';
                 }
             }
             $res = $model->store(['status'=>$status,'price'=>$price],$val['id']);
         }
     }

     public function getSupplier(&$param)
     {
         $this->autoSyncPrice();
         //选择不同的供应商
         $model = new Supplier();
         $list = $model->getList()->toArray();
         $info = [];
         if(empty($param['amount'])){
             $param['amount'] = 1;
         }

         //规则 获取所有满足条件的上游
         //$param['platform']
         //$list = Redis::hGetAll('h_supplier_price_'.$param['platform']);
         $supplier_list = [];
         $merchant_price = $param['price']/$param['amount'];

         $count = 0;

         $order = new \app\common\model\Order();
         $count_list = $order->field('count(id) as num,pgw_id')->where(['status'=>'Undelivered'])->order('id asc')->group('pgw_id')->select();
         $count_list = $count_list->toArray();
         foreach ($list as $key => $val){
             if($val['status'][$param['platform']] == 'online'){
                 $config = Redis::get('stock_'.$val['pgw']);
                 //stock_Exchange
                 if($val['price'][$param['platform']] <$merchant_price*self::RATE){
                     if(!empty($config)){
                         $config = json_decode($config,true);
                         if(isset($config['rule']) && isset($config['stock'])){
                             if($config['rule'][0]>$param['amount'] || ($param['amount']>$config['rule'][1] && $param['amount'] !=1)){
                                 Redis::hSet('log',time(),$val['pgw'].'不在价格范围'.json_encode(['config'=>$config,'param'=>$param]));
                                 continue;
                             }
                             if($config['stock'][$param['platform']]<$param['amount']){
                                 Redis::hSet('log',time(),$val['pgw'].'库存不足'.json_encode(['config'=>$config,'param'=>$param]));
                                 continue;
                             }
                         }
                     }
                     $flag = false;
                     if(!empty($count_list)){
                         foreach ($count_list as $k=>$v){
                             if($val['id'] == $v['pgw_id'] && $val['max_num']<=$v['num']){
                                 Redis::hSet('log',time(),$val['pgw'].'超出限制'.json_encode(['config'=>$val,'param'=>$param]));
                                 $flag = true;
                             }
                         }
                     }

                     if($flag){
                         continue;
                     }
                     $supplier_list[] = $val;
                     $count += intval($val['weight']);
                 }
             }
         }

         $changeNum = rand(0,$count-1);
         $count = 0;
         foreach ($supplier_list as $key =>$val){
             $count += intval($val['weight']);
             if($changeNum<=$count){
                 $info = $val;
                 break;
             }
         }

//         foreach ($list as $key => $val){
//             if($val['status'][$param['platform']] == 'online'){
//                 $pgw_price = round($val['price'][$param['platform']]*($param['amount']),2);
//                 if($pgw_price<$param['price']*self::RATE){
//                     if(!empty($info)){
//                         $old_price = round($info['price'][$param['platform']]*($param['amount']),2);
//                         if($pgw_price<=$old_price){
//                             $info = $val;
//                         }
//                     }else{
//                         $info = $val;
//                     }
//                 }
//             }
//         }
         $param['pgw_id'] = 0;
         if($info){
             $param['pgw_id'] = $info['id'];
             $param['pgw_payment'] = $info['pgw'];
             $param['pgw_price'] = round($info['price'][$param['platform']]*($param['amount']),2);
//             if($param['pgw_price']>=$param['price']*0.95){
//                 $param['pgw_id'] = 0;
//             }
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
