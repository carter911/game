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

        if($params['amount']<30 || $params['amount']>5000){
            echo '金额不对';
            return false;
        }
        $url = self::PGW_URL . 'createorder-ajax';
        $res = self::curlPost($url, $params, $data);
        if ($res != 200) {
            Log::error('exchange远程请求地址' . $url . var_export($res, true) . var_export($data, true));
            return false;
        }
        $data = json_decode($data, true);
        $data['pgw_return'] = json_encode($data);
        if($data['code']  == 200){
            $data['status'] = 'transferring';
            $data['pgw_order_id'] = $data['orderID'];
        }else{
            if($data['stringCode'] == 'user_or_pass'){
                $data['status'] = 'wrong login';
            }else if( $data['stringCode'] == 'no_backup_code'){
                $data['status'] = 'wrong no_backup_code';
            }else if( $data['stringCode'] == 'captcha'){
                $data['status'] = 'wrong captcha';
            }else if($data['stringCode'] == 'ps4_diabled' || $data['stringCode'] == 'xbox_diabled' ){
                //关闭考虑重新递送
            }
            else {
                $data['status'] = 'unexpected';
            }
            //'auth-error' - error while authenticating
            //'undefined' - undefined code, contact administrator
            //'no-stock' - there is no available stock left
            //'expired_session' - there is somebody logged on the account
            //'captcha' - captcha solving needed
            //'user_or_pass' - wrong credentials
            //'no_backup_code' - there's no backup code
            //'backup_code' - provided backup codes are wrong
            //'no_club', 'persona_not_found' - didn't find persona on that account
            //'market_disabled' - trade market is locked
            //'too_low_coins_amount' - minimum start balance is 200 coins
            //'tradepile_full' - tradepile is full, you need to make some space in there
            //'no-order' - Order wasn't found
            //'ps4_diabled' - ps4 orders are temporary disabled;
            //'xbox_diabled' - xbox orders are temporary disabled;

//                                <option  value="Undelivered">Undelivered</option>
//                                <option  value="delivered">[delivered]delivered</option>
//                                <option  value="transferring">[transferring]transfer in progress</option>
//                                <option  value="end">[end]order finished</option>
//                                <option  value="forbidden">[forbidden]EA web app is down</option>
//                                <option   value="captcha">[captcha]can’t solve captcha</option>
//                                <option  value="tradepilefull">[tradepilefull]there are no space no trade pile to move cards</option>
//                                <option  value="too many action">[too many action]too many action</option>
//                                <option  value="unexpected">[unexpected]technical issue on our site</option>
//                                <option  value="< 200 coins">[< 200 coins]less then 200 coins in account</option>
//                                <option  value="wrong backup">[wrong backup]wrong backup codes</option>
//                                <option  value="wrong login">[wrong login]login or password is wrong</option>
//                                <option  value="market locked">[market locked]web app market is locked</option>
//                                <option  value="csm online">[csm online]customer online</option>
//                                <option  value="no login verification">[no login verification]</option>
//                                <option  value="new">[new]new order not checked yet</option>
            Log::error('exchange返回异常' . $url . var_export($res, true) . var_export($data, true));
        }
        return $data;
    }

    public function queryOrder($orderInfo)
    {
        if (empty($orderInfo['pgw_prder_id'])) {
            echo '订单不存在';
            return false;
        }
        $params = self::getParam();
        $url = self::PGW_URL . 'checkorder-ajax';
        $res = self::curlPost($url, $params, $data);
        $data = json_decode($data, true);
        $data['pgw_return'] = json_encode($data);
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
