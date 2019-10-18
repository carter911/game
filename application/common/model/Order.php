<?php
namespace app\common\model;

 use think\Db;

 class Order extends \think\Model {


     protected $table = 'order';
     public function add()
     {

     }

     public function store($data, $id =0)
     {
         if($id>0){
             $data['update_at'] = time();
             //$data['price'] = round(session('merchant')['price']*$data['amount'],2);
             $res = $this->allowField(['status','price','update_at','pgw_return','pgw_gateway_id'])->save($data,['id'=>$id]);
             return $res;
         }


         $data['status'] = 'Undelivered';
         $data['create_at'] = time();
         $res = $this->allowField(true)->insert($data);
         if(empty($res)){
             return false;
         }
         return $this->getLastInsID();
     }

     public function getInfo($id)
     {
         return Db::name('order')->where(['id'=>$id])->find();
     }

     public function getList()
     {

     }

     public function cache()
     {

     }
 }
