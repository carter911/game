<?php
namespace app\merchant\controller;

use app\common\logic\Pgw;
use app\common\model\Merchant;
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
        $search = Request::instance()->param();


        if(!empty($search['supplier_id']) ){
            $where['pgw_id'] = trim($search['supplier_id']);
        }


        if(!empty($search['merchant_order_id']) ){
            $where['merchant_order_id'] = trim($search['merchant_order_id']);
        }

        if(!empty($search['status']) ){
            $where['status'] = trim($search['status']);
        }else{
            $search['status'] = "";
        }

        $merchant_id = session('merchant_id');
        $where['merchant_id'] = $merchant_id;

        $model = new Merchant();
        $info = $model->getInfo($merchant_id)->toArray();
        $list = Db::name('order')->where($where)->order('id desc')->paginate(20);
        $this->assign('list', $list);
        $this->assign('info', $info);
        $this->assign('search', $search);
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

        //找到最优质的的上游供货商
        $pgw = new Pgw();
        $pgw->getSupplier($param);
        $merchant = new Merchant();
        $merchant_info = $merchant->getInfo($this->merchant_id)->toArray();
        $param['price'] = round($merchant_info['price'][$param['platform']]*($param['amount']),2);

        if( $param['price']<=$param['pgw_price']){
            $this->error('create order failed');
        }

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
