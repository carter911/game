<?php

namespace app\common\gateway;

use app\common\logic\Pgw;
use app\common\model\Order;
use think\Log;

class Exchange extends Base
{
    protected $base_url = '';
    public $order = [];
    public function __construct($orderId = 0)
    {
        $model = new Order();
        if ($orderId > 0) {
            $this->order = $model->getInfo($orderId);
        }
    }

    //Alex / JvbJeyCoLQJ   API key: BtDsk86rCJ7H8nvOaDsVnPE64oCntdk

    const KEY = 'BtDsk86rCJ7H8nvOaDsVnPE64oCntdk';
    const USER_ID = 'Alex';
    const PGW_URL = 'https://fifa-exchange.com/';

    public static function getParam($action='getPrices')
    {
        $param = [
            'username'=>self::USER_ID,
            'hash'=>md5(self::USER_ID.self::KEY),
        ];
        return $param;
    }


    public function getPrice()
    {
        $data = [];
        try {
            $params = [];
            $url = self::PGW_URL.'getprices-ajax';
            $res = self::curlJson($url,self::getParam(),$data,[],'POST');
            if($res !=200){
                Log::error('exchange远程请求地址'.$url.var_export($res,true).var_export($data,true));
                return false;
            }
            $price = [];
            foreach ($data['prices'] as $key=> $item){
                if(in_array($key,Pgw::$gameType)){
                    $price[$key] = round($item/1000,3);
                    if($data['stock'][$key] <=0){
                        $price[$key] = 999;
                    }
                }
            }
            return $price;
        } catch (\Throwable $e) {
            Log::error('Exchange远程请求地址'.$e->getMessage());
            return [];
            //echo $e->getMessage();
        }
    }

    public function balance()
    {
        $params = [];
        foreach (self::getParam('getAmountOwed') as $key => $val){
            $params[] = $key."=".$val;
        }
        $params = implode("&",$params);
        $url = self::PGW_URL.'?'.$params;
        $res = self::curlJson($url,[],$data,[],'GET');
        if($res !=200){
            Log::error('Utloader远程请求地址'.$url.var_export($res,true).var_export($data,true));
            return false;
        }
        return $data;
    }


    public static function formatPlatform($p)
    {
        $platform = [
            'FFA20PS4'=>'PS',
            'FFA20XBO'=>'XBOX',
            'FFA20PCC'=>'PC',
        ];
        return isset($platform[$p])?$platform[$p]:'PS';
    }

    public function newOrder($orderInfo)
    {
        if(empty($orderInfo)){
            return false;
        }

        $params = self::getParam('addOrder');
        $params['email'] = trim($orderInfo['login']);
        $params['password'] = trim($orderInfo['password']);
        $params['platform'] = self::formatPlatform($orderInfo['platform']);
        $params['backup_code'] = $orderInfo['backup1'];
        $params['amount'] = $orderInfo['amount']/1000;
        $param = [];
        foreach ($params as $key => $val){
            $param[] = $key."=".$val;
        }
        $param = implode("&",$param);
        $data = [];
        $res = self::curlJson(self::PGW_URL."?".$param,[],$data,[],'GET');
        $data['pgw_return'] = json_encode($data);
        if($res !=200){
            Log::error('Utloader远程请求地址'.self::PGW_URL.var_export($res,true).var_export($data,true));
            return $data;
        }

        if($data['status'] ==1){
            $data['status'] = 'transferring';
        }else {
            if($data['coins_remaining'] == $data['transfer_amount']){
                $data['status'] = 'end';
            }else{
                $data['status'] = 'transferring';
            }
        }
        $data['pgw_message'] = isset($data['reason'])?$data['reason']:'';
        $data['pgw_order_id'] = isset($data['token'])?$data['token']:'';
        return $data;
    }

    public function queryOrder($orderInfo)
    {
        if(empty($orderInfo['pgw_prder_id'])){
            return false;
        }
        $params = self::getParam('trackOrder');
        $params['token'] = $orderInfo['pgw_prder_id'];
        $param = [];
        foreach ($params as $key => $val){
            $param[] = $key."=".$val;
        }
        $param = implode("&",$param);
        $data = [];
        $res = self::curlJson(self::PGW_URL."?".$param,[],$data,[],'GET');
        $data['pgw_return'] = json_encode($data);
        if($res !=200){
            Log::error('Utloader远程请求地址'.self::PGW_URL.var_export($res,true).var_export($data,true));
            return $data;
        }
        if($data['result'] ==200){
            if($data['coins_transferred'] == $data['transfer_amount']){
                $data['status'] = 'end';
            }
            $data['transaction_already_amount'] = $data['coins_transferred'];
        }else{
            echo '订单不存在';
            //TODO 应该是没有找到这个订单
            Log::error($data);
            exit;
            //$data['status'] = isset($data['reason'])?$data['reason']:'unexpected';
        }
        return $data;
    }
}
