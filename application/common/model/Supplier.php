<?php

namespace app\common\model;

use think\Cache;
use think\Model;

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
            if (empty($price[$val])) {
                $priceData[$val] = 999;
            } else {
                $priceData[$val] = $price[$val];
            }
        }
        return json_encode($priceData);
    }

    public function getPriceAttr($price)
    {
        $price = json_decode($price, true);
        $priceData = [];
        foreach ($this->gameType as $key => $val) {
            if (empty($price[$val])) {
                $priceData[$val] = 999;
            } else {
                $priceData[$val] = $price[$val];
            }
        }
        return $priceData;
    }


    public function setStatusAttr($price)
    {
        $priceData = [];
        foreach ($this->gameType as $key => $val) {
            if (empty($price[$val])) {
                $priceData[$val] = 'offline';
            } else {
                $priceData[$val] = $price[$val] == 'on' ? 'online' : 'offline';
            }
        }
        return json_encode($priceData);
    }

    public function getStatusAttr($price)
    {
        $price = json_decode($price, true);
        $priceData = [];
        foreach ($this->gameType as $key => $val) {
            if (empty($price[$val])) {
                $priceData[$val] = 'offline';
            } else {
                $priceData[$val] = $price[$val] == 'online' ? 'online' : 'offline';
            }
        }
        return $priceData;
    }

    public function store($data, $id = 0)
    {
        if ($id > 0) {
            $data['update_at'] = time();
            //$data['price'] = round(session('merchant')['price']*$data['amount'],2);
            $res = $this->allowField(['status', 'price', 'update_at', 'pgw_return', 'pgw_gateway_id'])->save($data, ['id' => $id]);
            return $res;
        }

        $data['status'] = 'Undelivered';
        $data['create_at'] = time();
        $res = $this->allowField(true)->insert($data);
        if (empty($res)) {
            return false;
        }
        return $this->getLastInsID();
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
            Cache::set('supplier_' . $info['id'] . '_' . $key, $val);
        }
    }
}
