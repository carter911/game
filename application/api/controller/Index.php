<?php
namespace app\api\controller;

use app\common\logic\Pgw;
use app\common\model\Merchant;
use app\common\model\Order;
use think\Db;
use think\Log;
use think\Request;
use think\Validate;
use tp5redis\Redis;

class Index extends Base
{

    protected $merchant = [];

    public function __construct()
    {
        $token = Request::instance()->param('user',0);
        $model = new Merchant();
        $merchant =  $model->where(['key'=>$token])->find();
        $merchant = $merchant->toArray();
        if(empty($merchant)){
            return retData(null,400,'auth error ');
        }
        $this->merchant = $merchant;
    }

    public function checkStatus($info)
    {
        //上游最低价格不能高于下游价格
        $price = Db::name('supplier')->where(['status'=>'online'])->order('price desc')->field('price')->find();
        $price = isset($price['price'])?$price['price']:0;
        if($info['price']<=$price){
            return false;
        }
        return true;
    }

    /**
     * 查询价格
     */
    public function price()
    {
//        Redis::set('name',111);
//        $name = Redis::get('name');
//        echo $name;die;
        $platform = Request::instance()->get('platform','FFA20PS4');
        $model = new Merchant();
        if(!in_array($platform,$model->gameType)){
            return retData(null,1001,'params error');
        }
        $param = ['platform'=>$platform,'price'=>$this->merchant['price'][$platform]];
        $model = new Pgw();
        $model->getSupplier($param);
        $status = 'offline';
        if($param['pgw_id']>0){
            $status = $param['status'];
        }
        return retData(['price'=>$this->merchant['price'][$platform],'platform'=>$platform,'status'=>$status]);
    }

    /**
     * 添加订单
     */
    public function newOrder()
    {
        $param = Request::instance()->only(['merchant_order_id','login','password','amount','platform','backup1','backup2','backup3']);
        Log::info($param);
        $param['merchant_id'] = $this->merchant['id'];
        if($param['platform'] == 'ps4'){
            $param['platform'] = 'FFA20PS4';
        }
        $rule = [
            'merchant_id'  => 'require',
            'merchant_order_id' => 'require',
            'login' => 'require',
            'amount' => 'require',
            'password' => 'require',
            'platform' => 'require',
            'backup1' => 'require',
            'backup2' => 'require',
            'backup3' => 'require',
        ];
        $msg = [
            'merchant_id.require' => 'merchant_id is require',
            'merchant_order_id.require'     => 'merchant_order_id is require',
            'login.require'     => 'login is require',
            'amount.require'     => 'amount is require',
            'password.require'  => 'password  is require',
            'platform.require'  => 'platform  is require',
            'backup1.require'  => 'backup1  is require',
            'backup2.require'  => 'backup2  is require',
            'backup3.require'  => 'backup3 is require',
        ];
        $validate =  new Validate($rule,$msg);
        if (!$validate->check($param)) {
            return retData(null,3001,$validate->getError());
        }
        if(!isset($this->merchant['status'][$param['platform']]) || $this->merchant['status'][$param['platform']] !=='online'){
            return retData(null,500,'gateway closed');
        }

        $param['price'] = round($this->merchant['price'][$param['platform']]*($param['amount']/1000),2);
        $pgw = new Pgw();
        $pgw->getSupplier($param);
        if($param['pgw_id']<=0){
            return retData(null,500,'stock empty');
        }

        $model = new Order();
        $id = $model->store($param);
        if(empty($id)){
            return retData(null,500,'create order failed');
        }

        $pgw->pgw($param);
        return retData(['id'=>$id,'time'=>time(),'status'=>'ORDER CREATE'],200,'create order success');
    }

    /**
     * 查询订单状态
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderStatus()
    {
        $param = Request::instance()->only(['order_id']);
        $model = new Order();
        $info = $model->getInfo($param['order_id'],$this->merchant['id']);
        //$info = $model->field('id,status')->where(['id'=>$param['order_id'],'merchant_id'=>$this->merchant['id']])->find();
        if(empty($info)){
            return retData(null,200,'error order_id');
        }
        Log::info($info);
        $data['num'] = $info['amount'];
        if(!isset($info['transaction_already_ammount'])){
            $data['transaction_already_amount'] = 0;
        }
        if($info['status']  == 'end'){
            //$info['num'] = 100;
            $data['transaction_already_amount'] = $info['amount'];
        }
        $data['amount'] = $info['amount'];
        $data['status'] = $info['status'];
        $data['id'] = $info['id'];
        return retData($data,200,'success');
    }


}
