<?php
namespace app\api\controller;

use app\common\model\Order;
use think\Db;
use think\Log;
use think\Request;
use think\Validate;

class Index extends Base
{

    protected $merchant = [];

    public function __construct()
    {
        $token = Request::instance()->param('user',0);
        $merchant =  Db::name('merchant')->where(['key'=>$token])->find();
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

        $param = Request::instance()->get('platform','ps4');
        if(!$this->checkStatus($this->merchant)){
            $this->merchant['status'] = 'offline';
        }else{
            $this->merchant['status'] = 'online';
        }
        return retData(['price'=>$this->merchant['price'],'status'=>$this->merchant['status']]);
    }

    /**
     * 添加订单
     */
    public function newOrder()
    {
        if(!$this->checkStatus($this->merchant)){
            return retData(null,500,'stock empty');
        }

        $param = Request::instance()->only(['merchant_order_id','login','password','amount','platform','backup1','backup2','backup3']);
        Log::info($param);
        $param['merchant_id'] = $this->merchant['id'];
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
        $model = new Order();
        $param['price'] = round($this->merchant['price']*$param['amount'],2);

        //找到最优质的的上游供货商
        $info = Db::name('supplier')->where(['status'=>'online'])->order('price asc')->field('id,price')->find();
        if($info){
            //Undelivered
            $param['pgw_id'] = $info['id'];
            $param['pgw_price'] = round($info['price']*$param['amount'],2);
            $id = $model->store($param);
            if(empty($id)){
                return retData(null,500,'create order failed');
            }
        }else{
            return retData(null,500,'stock empty');
        }
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
        $info = $model->field('id,status')->where(['id'=>$param['order_id'],'merchant_id'=>$this->merchant['id']])->find();
        $info['num'] = 0;
        if(empty($info['transaction_already_ammount'])){
            $info['transaction_already_ammount'] = 0;
        }
        if($info['status']  == 'end'){
            //$info['num'] = 100;
            $info['transaction_already_amount'] = $info['amount'];
        }
        return retData($info,200,'success');
    }


}
