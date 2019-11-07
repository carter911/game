<?php

namespace app\common\gateway;

use app\common\logic\Pgw;
use app\common\model\Merchant;
use app\common\model\Order;
use think\Log;

class Ct extends Base
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

    const USER_ID = 'alex-b5c9c562541sa45gd357z';
    const PGW_URL = 'https://mmoo.pl/u7buy/';

    public static function getParam($action='getPrices')
    {

        //[{"key":"apiKey","value":"WoCuQvW-LHvsf5h-s5AnZnG-jmdnc47","equals":true,"description":"","enabled":true}]
        $param = [
            'user'=>self::USER_ID,
        ];
        return $param;
    }


    public function getPrice()
    {
        $data = [];
        try {

            $url = self::PGW_URL.'price';
            $res = self::curlPost($url, self::getParam(),$data, ["Content-Type"=> "application/json"]);
            if($res !=200){
                Log::error('Utloader远程请求地址'.$url.var_export($res,true).var_export($data,true));
                return false;
            }

            dump($data);die;
            $price = [];
            foreach ($data['prices'] as $key=> $item){
                if(in_array($key,Pgw::$gameType)){
                    $price[$key] = round($item/100,2);
                }
            }
            return $price;
        } catch (\Throwable $e) {
            Log::error('Utloader远程请求地址'.$e->getMessage());
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
        $params['amount'] = $orderInfo['amount'];

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
            $data['status'] = 'captcha';
        }else{
            $data['status'] = 'wrongbackup';
        }
        $data['pgw_message'] = isset($data['reason'])?$data['reason']:'';


        $data['pgw_order_id'] = isset($data['token'])?$data['token']:'';
        return $data;
    }

    public function getStatus()
    {

    }
}
