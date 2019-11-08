<?php
namespace app\common\gateway;

use app\common\model\Order;
use think\exception\ErrorException;
use think\Log;

class GTE extends Base {

    protected $base_url = 'https://www.utdeliver.com/api/20/';
    protected $account = 'gte';
    protected $password = 'nsuVeoYt5p1o40zc0BbB';
    public $order = [];
    public function __construct($orderId=0)
    {
        $model = new Order();
        if($orderId>0){
            $this->order = $model->getInfo($orderId);
        }
    }

    public function getHeader()
    {
        return ['Authorization: Basic '.base64_encode($this->account.':'.$this->password)];
    }

    public function query($query=['platform'=>'ps4'])
    {
        $data = [
           'price'=>9999,
           'platform'=>$query['platform'],
        ];
        $resData = [];
        try{
            $param = [];
            $res = self::curlPost($this->base_url.'price/'.$query['platform'],$param,$reData,$this->getHeader());

            if($res != 200 || empty($resData)){
                Log::error('请求失败'.var_export($resData,true));
                return $data;
            }
            $resData  = json_decode($resData,true);
            $data['price']= isset($resData['price'])?$resData['price']:9999;
            return $data;
        }catch (\Throwable $e){
            Log::error($e->getMessage());
            echo $e->getMessage();
           return [];
        }
    }

    public function price()
    {

    }

    public function addOrder()
    {
        //curl
        try{
            $this->order['pgw_error_code'] = 500;
            $resData = [];
            $param = [
                'platform'=> trim($this->order['platform']),
                'email'=> trim($this->order['login']),
                'password'=> trim($this->order['password']),
                'backupCode'=> trim($this->order['backup1']),
                'amount'=> trim($this->order['amount']),
                'orderId'=>  trim('game_'.$this->order['id']),
                //'ownStock'=>$this->order['platform'],
            ];
            $res = self::curlPost($this->base_url.'comfort/add',$param,$resData,$this->getHeader());
            $this->order['pgw_return'] = $resData;
            Log::error('订单请求'.$res.var_export($resData,true));
            $this->order['pgw_error_code'] = $res;
            if($res != 200 || empty($resData)){
                $this->order['pgw_error_message'] = $resData;
                Log::error('请求失败'.var_export($resData,true));
                return $this->order;
            }

            $resData = json_decode($resData,true);
            if($resData['success'] !== true){
                $this->order['pgw_error_code'] = 301;
                $this->order['pgw_error_message'] = $resData['error'];
            }
            //TODO

            return $this->order;
        }catch (\Throwable $e){
            return $this->order;
        }
    }

    public function OrderStatus()
    {
        try{
            $this->order['pgw_error_code'] = 500;
            $resData = [];
            $param = [
                'platform'=>$this->order['platform'],
                'email'=>$this->order['login'],
                'password'=>$this->order['password'],
                'backupCode'=>$this->order['backup1'],
                'amount'=>$this->order['amount'],
                'orderId'=> 'game_'.$this->order['id'],
                //'ownStock'=>$this->order['platform'],
            ];
            $res = self::curlPost($this->base_url.'comfort/add', $param, $resData, $this->getHeader());
            $this->order['pgw_return'] = $resData;
            Log::error('订单请求'.$res.var_export($resData,true));
            $this->order['pgw_error_code'] = $res;
            if($res != 200 || empty($resData)){
                $this->order['pgw_error_message'] = $resData;
                Log::error('请求失败'.var_export($resData,true));
                return $this->order;
            }

            $resData = json_decode($resData,true);
            if($resData['success'] !== true){
                $this->order['pgw_error_code'] = 301;
                $this->order['pgw_error_message'] = $resData['error'];
            }
            //TODO

            return $this->order;
        }catch (\Throwable $e){
            return $this->order;
        }
    }
}
