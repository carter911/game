<?php

namespace app\common\model;

 use think\Model;

 class TraitModel{

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
         return $this->where(['id'=>$id])->find();
     }

     public function getList($where)
     {
         return $this->where($where)->find();
     }

     public function cache()
     {

     }
 }
