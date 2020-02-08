<?php

namespace app\common\gateway;

use app\common\logic\Pgw;
use app\common\model\Merchant;
use app\common\model\Order;
use think\Log;
use tp5redis\Redis;

class FutFill extends Base
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

    const USER_ID = '64829da2-e3c0-43ad-b4f1-553352edd6ca';
    const PGW_URL = 'https://futservices.com/FUTFilllWebServiceV5/FUTFillWebService.asmx/';

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
            $url = self::PGW_URL.'Stock';
            $res = self::curlPost($url, json_encode(self::getParam()),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);
            if($res !=200){
                Log::error('futFill远程请求地址'.$url.var_export($res,true).var_export($data,true));
                return false;
            }

            $data = json_decode($data,true);
            $price = [];
            $stock = [];
            foreach ($data['d'] as $key=> $item){
                if(in_array($item['SKU'],self::$gameType)){
                    if($item['Credits']>0){
                        $price[$item['SKU']] = round($item['Price']/1000,4);
                    }else{
                        $price[$item['SKU']] = 999;
                    }
                    $stock[$item['SKU']] = $item['Credits'];
                }
            }
            foreach (self::$gameType as $key => $val){
                if(!isset($price[$val])){
                    $price[$val] = 999;
                }
            }
            Redis::set('stock_FutFill',json_encode(['rule'=>[0,100000],'stock'=>$stock]));
            Redis::set('stock_FutFill_price',json_encode($price));
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


    public function newOrder($orderInfo)
    {
        if(empty($orderInfo)){
            return false;
        }

        $params = self::getParam();
        $params['customer'] = [
            "username"=>trim($orderInfo['login']),
            "password"=>trim($orderInfo['password']),
            "phishing"=>trim('abcd'),
            "backupCode"=>trim($orderInfo['backup2']),
        ];
        $params['platform'] = $orderInfo['platform'];
        //$params['backup_code'] = $orderInfo['backup1'];
        $params['amount']  = intval($orderInfo['amount']);
        $params['backup1'] = $orderInfo['backup1'];
        $params['backup2'] = $orderInfo['backup2'];
        $params['charges'] = $params['amount']*0.056;
        $params['note']   = '';
        $data = [];
        $url = self::PGW_URL.'New';
        $res = self::curlPost($url, json_encode($params),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);
        if($res !=200){
            Log::error('FutFill新建订单请求地址'.$url.var_export($res,true).var_export($data,true));
            return false;
        }
        $data = json_decode($data,true);
        $data['pgw_return'] = json_encode($data);
        $data['pgw_order_id'] = isset($data['d'])?$data['d']:'0';
        $data['status'] = 'new';
        return $data;
    }

    public function queryOrder($orderInfo)
    {

        $url = self::PGW_URL.'Status';
        $user = 'alex-b5c9c562541sa45gd357z';

        $payload = array(
            'user' => self::USER_ID,
            'token' => $orderInfo['pgw_order_id']
        );
        $res = self::curlPost($url, json_encode($payload),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);
        if($res !=200){
            Log::error('Utloader远程请求地址'.$url.var_export($res,true).var_export($data,true));
            return false;
            exit;
        }
        $data = json_decode($data,true);
        $data['pgw_return'] = json_encode($data);

        $status = $data['Status'];
        if(!isset($status)){
            return $data;
        }

        if($status=0){
            if($data['Amount']>=$data['Delivered']){
                $data['status'] = 'end';
            }else{
                $data['status'] = 'transferring';
            }
            $data['transaction_already_amount'] = isset($data['Delivered'])?$data['Delivered']:0;
        }
        else if($status>0 && $status <= 200){
//            if($data['Amount']>=$data['Delivered']){
//                $data['status'] = 'end';
//            }else{
//                $data['status'] = 'transferring';
//            }
//            $data['transaction_already_amount'] = isset($data['Delivered'])?$data['Delivered']:0;
        }else{
            $data['status'] = self::formatStatus($status);
        }
        return $data;

    }

    public static function formatStatus($status= "")
    {
        $statusList = [
            201=>'wronglogin',
            210=>'marketlocked',
            211=>'tradepilefull',
            212=>'marketlocked',
            401=>'wronglogin',
            402=>'wrongbackup',
            404=>'wronglogin',
            405=>'csmonline',
            407=>'nologinverification',
            417=>'captcha',
            418=>'marketlocked',
            419=>'marketlocked',
            420=>'marketlocked',
            429=>'toomanyaction',
            1001=>'nostock',
            1010=>'unexpected',
        ];
        return isset($statusList[$status])?$statusList[$status]:'';
    }
}
