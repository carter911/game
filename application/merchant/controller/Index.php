<?php
namespace app\merchant\controller;

use app\common\model\Order;
use think\Db;
use think\Request;
use think\Validate;

class Index extends Base
{

    public function __construct()
    {
        parent::__construct();
        $supplier = session('merchant_id');
        $action = Request::instance()->action();
        if(empty($supplier) && !in_array($action,['login','checklog'])){
            $this->redirect('login');
        }
    }

    /**
     * @return mixed
     */
    public function login()
    {
        return $this->fetch();
    }

    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function checkLog()
    {
        $param = Request::instance()->only(['user_name','password']);
        $info = Db::name('merchant')->where(['name'=>$param['user_name']])->find();
        if(!$info){
            $this->error('account or password error');
        }
        if(hash('sha256',$param['password']) !=$info['password']){
            $this->error('account or password error');
        }
        session('merchant_id',$info['id']);
        session('merchant',$info);
        $this->success('login success','index');
    }

    public function logout()
    {
        session('admin_id','');
        session('admin','');
        $this->success('logout success','login');
    }


    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function index( )
    {
        $merchant_id = session('merchant_id');
        $info = Db::name('merchant')->find($merchant_id);

        if(!$this->checkStatus($info)){
            $info['status'] = 'offline';
        }else{
            $info['status'] = 'online';
        }
        $list = Db::name('order')->where(['merchant_id'=>$merchant_id])->order('id desc')->paginate(20);
        $this->assign('list', $list);
        $this->assign('info', $info);
        return $this->fetch('index');
    }

    public function checkStatus($info)
    {
        //上游最低价格不能高于下游价格
        $price = Db::name('supplier')->where(['status'=>'online'])->order('price asc')->field('price')->find();
        $price = isset($price['price'])?$price['price']:0;
        if($info['price']<=$price){
            return false;
        }
        return true;
    }

    public function getSupplierIdByPrice()
    {
        $price = Db::name('supplier')->where(['status'=>'online'])->order('price asc')->field('id')->find();
        return  isset($price['id'])?$price['id']:0;
    }


    /**
     * 添加订单
     */
    public function store()
    {
        $param = Request::instance()->only(['merchant_order_id','login','password','amount','platform','backup1','backup2','backup3']);
        $param['merchant_id'] = session('merchant_id');
        $rule = [
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
            $this->error($validate->getError());
        }
        $param['price'] = round(session('merchant')['price']*($param['amount']/1000),2);
        //找到最优质的的上游供货商
        $info = Db::name('supplier')->where(['status'=>'online'])->order('price asc')->field('id,price')->find();
        //Undelivered
        $param['pgw_id'] = $info['id'];
        $param['pgw_price'] = round($info['price']*($param['amount']/1000),2);

        $model = new Order();
        $id = $model->store($param);
        if(empty($id)){
            $this->error('create order failed');
        }
        $this->success('create success');
    }

    public function api()
    {
        return $this->fetch();
    }



}
