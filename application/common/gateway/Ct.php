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

    public static function getParam()
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
            $res = self::curlPost($url, json_encode(self::getParam()),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);
            if($res !=200){
                Log::error('Utloader远程请求地址'.$url.var_export($res,true).var_export($data,true));
                return false;
            }
            $data = json_decode($data,true);
            $price = [];
            foreach ($data as $key=> $item){
                if($key == 'ps4'){
                    $price['FFA20PS4'] = round($item/1000,2);
                }

                if($key == 'xbox'){
                    $price['FFA20XBO'] = round($item/1000,2);
                }

                if($key == 'pcc'){
                    $price['FFA20PCC'] =  round($item/1000,2);
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
        return true;
    }


    public static function formatPlatform($p)
    {
        $platform = [
            'FFA20PS4'=>'ps4',
            'FFA20XBO'=>'xbox',
            'FFA20PCC'=>'pcc',
        ];
        return isset($platform[$p])?$platform[$p]:'PS';
    }

    public function newOrder($orderInfo)
    {
        if(empty($orderInfo)){
            return false;
        }

        $params = self::getParam();
        $params['login'] = trim($orderInfo['login']);
        $params['password'] = trim($orderInfo['password']);
        $params['platform'] = self::formatPlatform($orderInfo['platform']);
        $params['backup_code'] = $orderInfo['backup1'];
        $params['amount']  = intval($orderInfo['amount']/1000);
        $params['backup1'] = $orderInfo['backup1'];
        $params['backup2'] = $orderInfo['backup2'];
        $params['backup3'] = $orderInfo['backup3'];
        $params['igvID']   = 'alex-'.$orderInfo['id'];
        $data = [];
        $url = self::PGW_URL.'new';
        $res = self::curlPost($url, json_encode($params),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);

        if($res !=200){
            Log::error('Utloader远程请求地址'.$url.var_export($res,true).var_export($data,true));
            return false;
        }

        $data = json_decode($data,true);
        $data['pgw_return'] = json_encode($data);
        $data['pgw_order_id'] = isset($data['orderid'])?$data['orderid']:'0';
        if($data['code'] ==200){
            $data['status'] = 'transferring';
        }else{
            $data['code'] = 'wrongbackup';
        }
        //$data['pgw_message'] = isset($data['reason'])?$data['reason']:'';
        return $data;
    }

    public function queryOrder($orderInfo)
    {

        $url = 'https://mmoo.pl/u7buy/status';
        $user = 'alex-b5c9c562541sa45gd357z';
        $payload = array(
            'user' => $user,
            'orderid' => $orderInfo['pgw_order_id']
        );
        $res = self::curlPost($url, json_encode($payload),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);
        if($res !=200){
            Log::error('Utloader远程请求地址'.$url.var_export($res,true).var_export($data,true));
            return false;
            exit;
        }
        $data = json_decode($data,true);
        $data['pgw_return'] = json_encode($data);
        if($data['code'] != 200){
            return false;
            exit;
        }
        return $data;

    }
}
