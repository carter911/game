<?php

namespace app\common\model;

use think\Cache;
use think\Log;
use think\Model;
use tp5redis\Redis;

class Supplier extends Model
{
    protected $resultSetType = 'collection';
    public $gameType = [
        'FFA20PS4', 'FFA20XBO', 'FFA20PCC'
    ];

    protected $table = 'supplier';


    public function setPriceAttr($price)
    {
        $priceData = [];
        foreach ($this->gameType as $key => $val) {
            if (!isset($price[$val])) {
                $priceData[$val] = 999;
            } else {
                $priceData[$val] = $price[$val];
            }
        }
        $data = json_encode($priceData);
        //Log::info(var_export($data,true));
        //dump(json_encode($priceData));
        return $data;
    }

    public function getPriceAttr($price)
    {
        $price = json_decode($price, true);
        $priceData = [];
        foreach ($this->gameType as $key => $val) {
            if (!isset($price[$val])) {
                $priceData[$val] = 999;
            } else {
                $priceData[$val] = $price[$val];
            }
        }
        return $priceData;
    }


    public function setStatusAttr($status)
    {
        $statusData = [];
        foreach ($this->gameType as $key => $val) {

            if (empty($status[$val])) {
                $statusData[$val] = 'offline';
            } else {
                $statusData[$val] = ($status[$val] == 'on' || $status[$val] == 'online') ? 'online' : 'offline';
            }
        }
        $data = json_encode($statusData);
        //dump(json_encode($statusData));
        //Log::info(var_export($data,true));
        return $data;
    }

    public function getStatusAttr($status)
    {
        $status = json_decode($status, true);
        $priceData = [];
        foreach ($this->gameType as $key => $val) {
            if (empty($status[$val])) {
                $priceData[$val] = 'offline';
            } else {
                $priceData[$val] = $status[$val] == 'online' ? 'online' : 'offline';
            }
        }
        return $priceData;
    }

    public function store($data, $id = 0)
    {
        if ($id > 0) {
            $data['update_at'] = time();
            //$res = $this->allowField(true)->($data, ['id' => $id]);
            $res = $this->allowField(['status', 'price', 'update_at', 'pgw_return', 'pgw_gateway_id','currency','balance'])->save($data, ['id' => $id]);
            //Log::info(var_export(json_encode($this->getLastSql()),true));
            //echo $this->getLastSql();
            //$this->cache($id);
            return $res;
        }
        $data['create_at'] = time();
        $res = $this->allowField(true)->save($data);
        if (empty($res)) {
            return false;
        }
        $id = $this->getLastInsID();
        $this->cache($id);
        return $id;
    }

    public function getInfo($id)
    {
        return $this->where(['id' => $id])->find();
    }

    public function getList($where = [])
    {
        return $this->where($where)->select();
    }

    public function cache($id)
    {
        $info = $this->getInfo($id)->toArray();
        foreach ($info['price'] as $key => $val) {
            if($info['status'][$key] == 'online'){
                Redis::hSet('h_supplier_price_'.$key,$info['id'],$val);
            }else{
                Redis::hDel('h_supplier_price_'.$key,$info['id']);
            }
            Cache::set('supplier_' . $info['id'] . '_' . $key, $val);
        }
    }
}
