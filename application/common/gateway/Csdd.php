<?php

namespace app\common\gateway;

use app\common\logic\Pgw;
use app\common\model\Merchant;
use app\common\model\Order;
use think\Log;
use tp5redis\Redis;

class Csdd extends Base
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

    const USER_ID   = 'id2299';
    const USER_KEY  = 'WRgTFPIvMRskH3t8';
    const PGW_URL   = 'https://cdss.machineword.com/buyer_interface/';

    public static function getParam()
    {
        //[{"key":"apiKey","value":"WoCuQvW-LHvsf5h-s5AnZnG-jmdnc47","equals":true,"description":"","enabled":true}]
        $time = time();
        $hash = md5(self::USER_ID.'comfort_ps4'.$time.self::USER_KEY);
        $param = [
            'user'  => self::USER_ID,
            'data'  => ['type'=>'comfort_ps4'],
            'timestamp' => $time,
            'hash'  => $hash,
        ];
        return $param;
    }


    public function getPrice()
    {

        $data = [];
        try {
            $url = self::PGW_URL.'/price/';

            dump(json_encode(self::getParam()));
            $res = self::curlPost($url, json_encode(self::getParam()),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);
            if($res !=200){
                Log::error('futFill远程请求地址'.$url.var_export($res,true).var_export($data,true));
                return false;
            }
            dump($res);
            dump($data);die;
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
                    $stock[$item['SKU']] = $item['Credits']*1000;
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
        $params['amount']  = intval($orderInfo['amount']*1000);
        $params['backup1'] = $orderInfo['backup1'];
        $params['backup2'] = $orderInfo['backup2'];
        $price = Redis::get('stock_FutFill_price');
        $price= json_decode($price,true);
        $price = isset($price[$orderInfo['platform']])?$price[$orderInfo['platform']]:0.058;
        if($price>=999){
            $price = 0.058;
        }
        $params['charges'] = $orderInfo['amount']*$price;
        $params['note']   = '';
        $data = [];
        $url = self::PGW_URL.'New';
        $res = self::curlPost($url, json_encode($params),$data, ['X-AjaxPro-Method:ShowList', 'Content-Type: application/json; charset=utf-8',]);
        Redis::hSet('gateway_log'.date("Y-m-d"),$orderInfo['id'].'_'.time(),$data);
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
        Redis::hSet('gateway_query_log'.date("Y-m-d"),$orderInfo['id'].'_'.time(),$data);

        if($res !=200){
            Log::error('Utloader远程请求地址'.$url.var_export($res,true).var_export($data,true));
            return false;
            exit;
        }

        $data = json_decode($data,true);
        $data['pgw_return'] = json_encode($data);
        $data = $data['d'];
        $status = $data['Status'];
        if(!isset($status)){
            return $data;
        }

        if($status<=200){
            if($data['Amount']<=$data['Delivered']){
                $data['status'] = 'end';
            }else{
                $data['status'] = 'transferring';
            }
        }
        else{
            $data['status'] = self::formatStatus($status);
        }
        $data['transaction_already_amount'] = isset($data['Delivered'])?$data['Delivered']:0;
        return $data;

    }

    public static function formatStatus($status= "")
    {
        $statusList = [
            0=>'end',
            11=>'transferring',
            12=>'transferring',
            13=>'checking info',
            201=>'csmonline',
            210=>'marketlocked',
            211=>'tradepilefull',
            212=>'<200 coins',
            401=>'wronglogin',
            402=>'wrongbackup',
            404=>'unexpected',
            405=>'unexpected',
            407=>'nologinverification',
            417=>'captcha',
            418=>'unexpected',
            419=>'forbidden',
            420=>'unexpected',
            429=>'toomanyaction',
            1001=>'unexpected',
            1010=>'unexpected',
        ];
        return isset($statusList[$status])?$statusList[$status]:'new';
    }
}
