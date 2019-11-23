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

    public static function formatPlatform($p)
    {
        $platform = [
            'FFA20PS4' => 'ps4',
            'FFA20XBO' => 'xbox',
        ];
        return isset($platform[$p]) ? $platform[$p] : 'ps4';
    }


    public $formatPlatformBySupplier = [
        'ps4' => 'FFA20PS4',
        'xbox' => 'FFA20XBO',
    ];
    //Alex / JvbJeyCoLQJ   API key: BtDsk86rCJ7H8nvOaDsVnPE64oCntdk


    const KEY = 'BtDsk86rCJ7H8nvOaDsVnPE64oCntdk';
    const USER_ID = 'Alex';
    const PGW_URL = 'https://fifa-exchange.com/';

    public static function getParam($action = 'getPrices')
    {
        $param = [
            'username' => self::USER_ID,
            'hash' => md5(self::USER_ID . self::KEY),
        ];
        return $param;
    }


    public function getPrice()
    {
        $data = [];
        try {
            $params = [];
            $url = self::PGW_URL . 'getprices-ajax';
            $res = self::curlPost($url, self::getParam(), $data);
            if ($res != 200) {
                Log::error('exchange远程请求地址' . $url . var_export($res, true) . var_export($data, true));
                return false;
            }

            $data = json_decode($data, true);
            $price = [];
            foreach ($data['prices'] as $key => $item) {

                if (in_array($key, array_keys($this->formatPlatformBySupplier))) {
                    $price[$this->formatPlatformBySupplier[$key]] = round($item / 1000, 3);
                    if ($data['stock'][$key] <= 0) {
                        $price[$this->formatPlatformBySupplier[$key]] = 999;
                    }
                }
            }
            return $price;
        } catch (\Throwable $e) {
            Log::error('Exchange远程请求地址' . $e->getMessage());
            return [];
            //echo $e->getMessage();
        }
    }

    public function balance()
    {

        return [];
    }

    public function newOrder($orderInfo)
    {
        if (empty($orderInfo)) {
            return false;
        }
        $params = self::getParam();
        $params['email'] = trim($orderInfo['login']);
        $params['password'] = trim($orderInfo['password']);
        $params['platform'] = self::formatPlatform($orderInfo['platform']);
        $params['backup'] = json_encode([$orderInfo['backup1'],$orderInfo['backup2'],$orderInfo['backup3']]);
        $params['amount'] = $orderInfo['amount'] / 1000;
        $url = self::PGW_URL . 'createorder-ajax';
        $res = self::curlPost($url, $params, $data);
        if ($res != 200) {
            Log::error('exchange远程请求地址' . $url . var_export($res, true) . var_export($data, true));
            return false;
        }
        $data['pgw_return'] = ($data);
        $data = json_decode($data, true);
        if($data['code']  == 200){
            $data['status'] = 'transferring';
        }else{
            Log::error('exchange返回异常' . $url . var_export($res, true) . var_export($data, true));
            return false;
        }
        $data['pgw_order_id'] = isset($data['orderID']) ? $data['orderID'] : '';
        return $data;
    }

    public function queryOrder($orderInfo)
    {
        if (empty($orderInfo['pgw_prder_id'])) {
            return false;
        }
        $params = self::getParam();
        $url = self::PGW_URL . 'checkorder-ajax';
        $res = self::curlPost($url, $params, $data);
        $data['pgw_return'] = ($data);
        if ($res != 200) {
            Log::error('exchange远程请求地址' . $url . var_export($res, true) . var_export($data, true));
            return $data;
        }
        if ($data['code'] == 200 ) {
            if($data['status']  == 'Finished'){
                $data['status'] = 'end';
            }
            $data['transaction_already_amount'] = $data['transferred_amount'];
        } else {
            echo '订单不存在';
            //TODO 应该是没有找到这个订单
            Log::error($data);
            exit;
            //$data['status'] = isset($data['reason'])?$data['reason']:'unexpected';
        }
        return $data;
    }
}
