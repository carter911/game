<?php
namespace app\common\gateway;

use app\common\model\Order;

class Ct extends Base {

    protected $base_url = '';
    public $order = [];
    public function __construct($orderId=0)
    {
        $model = new Order();
        if($orderId>0){
            $this->order = $model->getInfo($orderId);
        }
    }
    public function query()
    {
        $data = [];
        try{
            $param = [
                'user'=>'IGV-30d1a272be59a3962fe7670fc',
                'orderid'=>'WEN1909242203785-0102',
            ];
            $res = self::curlPost('https://mmoo.pl/igv/status?user=IGV-30d1a272be59a3962fe7670fc',$param,$data);
            print_r($data);
            print_r($res);
        }catch (\Throwable $e){
            echo $e->getMessage();
        }
    }

    public function price()
    {

    }

    public function addOrder()
    {
        //curl


        $this->order['pgw_gateway_id'] = time();
        return $this->order;
    }

    public function OrderStatus()
    {

    }
}
