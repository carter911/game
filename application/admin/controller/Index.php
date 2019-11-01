<?php
namespace app\admin\controller;

use app\common\model\Merchant;
use app\common\model\Order;
use app\common\model\Supplier;
use think\Db;
use think\Log;
use think\Request;
use think\Validate;

class Index extends Base
{

    public function __construct()
    {
        parent::__construct();


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
        $info = Db::name('admin')->where(['name'=>$param['user_name']])->find();
        if(!$info){
            $this->error('account or password error');
        }
        if(hash('sha256',$param['password']) !=$info['password']){
            $this->error('account or password error');
        }
        session('admin_id',$info['id']);
        session('admin',$info);
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
        $where = [];
        if(!empty($search['supplier_id']) ){
            $where['pgw_id'] = trim($search['supplier_id']);
        }

        if(!empty($search['merchant_id']) ){
            $where['merchant_id'] = trim($search['merchant_id']);
        }

        if(!empty($search['merchant_order_id']) ){
            $where['merchant_order_id'] = trim($search['merchant_order_id']);
        }

        if(!empty($search['status']) ){
            $where['status'] = trim($search['status']);
        }else{
            $search['status'] = "";
        }

        $list = Db::name('order')->where($where)->order('id desc')->paginate(20)->each(function($item, $key){
            if(!empty($item['image'])){
                $item['image'] = Request::instance()->domain().'/uploads/'.$item['image'];
            }else{

            }

            return $item;
        });
        $this->assign('list', $list);
        $this->assign('search', $search);
        return $this->fetch('index');
    }


    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     */
    public function supplier( )
    {
        $list = Db::name('Supplier')->where([])->order('id desc')->paginate(10);
        $this->assign('list', $list);
        return $this->fetch('supplier');
    }

    public function add_supplier()
    {
        $info['price'] = [];
        $info['status'] = [];
        $this->assign('info',$info);
        $model = new Merchant();
        foreach ($model->gameType as $key => $val){
            $info['price'][$val] = 999;
            $info['status'][$val] = 999;
        }
        return $this->fetch('supplier_info');
    }

    public function update_supplier()
    {
        $id = Request::instance()->param('id',0);

        $model = new Supplier();
        $info = $model->getInfo($id)->toArray();
        $this->assign('info',$info);
        return $this->fetch('supplier_info');
    }


    public function store_supplier()
    {
        $param = Request::instance()->only(['id','name','password','price','status']);
        $rule = [
            'name' => 'require',
            //'password' => 'require',
            'price' => 'require',
            //'status' => 'require',
        ];
        $validate =  new Validate($rule);
        if (!$validate->check($param)) {
            $this->error($validate->getError());
        }
        $param['update_at'] = time();
        if(!empty($param['password'])){
            $param['password'] =  hash('sha256',$param['password']);
        }else if(empty($param['password']) && $param['id'] <=0){
            $param['password'] =  hash('sha256','game2019');
        }
        $model = new Supplier();
        if(isset($param['id']) && $param['id']>0){
            if(empty($param['password'])){
                unset($param['password']);
            }
            $model->update($param);
            $model->cache($param['id']);
        }else{
            $param['create_at'] = time();
            $id = $model->insert($param,false,true);
            $model->cache($id);
        }
        $this->success('success','supplier');
    }


    public function del_supplier()
    {
        $id = Request::instance()->param('id',0);
        Db::name('supplier')->delete(['id'=>$id]);
        $this->success('delete supplier success');
    }




    public function merchant()
    {
        $list = Db::name('merchant')->where([])->order('id desc')->paginate(20);
        $this->assign('list', $list);
        return $this->fetch('merchant');
    }

    public function add_merchant()
    {
        $this->assign('info',[]);
        return $this->fetch('merchant_info');
    }

    public function update_merchant()
    {
        $id = Request::instance()->param('id',0);
        $model = new Merchant();
        $info =$model->getInfo($id)->toArray();
        $this->assign('info',$info);
        return $this->fetch('merchant_info');
    }


    public function store_merchant()
    {
        $param = Request::instance()->only(['id','name','password','price','status']);
        $rule = [
            'name' => 'require',
            //'password' => 'require',
            'price' => 'require',
            //'status' => 'require',
        ];

        $validate =  new Validate($rule);
        if (!$validate->check($param)) {
            $this->error($validate->getError());
        }
        $param['update_at'] = time();
        $password = trim($param['password']);
        if(!empty($password)){
            Log::notice($password);
            $param['password'] =  hash('sha256',$password);
        }
        $model = new Merchant();
        if(isset($param['id']) && $param['id']>0){
            if(empty($param['password'])){
                unset($param['password']);
            }
            $model->update($param);
        }else{
            if(empty($param['password'])){
                $param['password'] =  hash('sha256','game2019');
            }
            $param['create_at'] = time();
            $param['key'] = hash('sha256',$param['name']);
            $model->insert($param);
        }
        $this->success('success','merchant');
    }


    public function del_merchant()
    {
        $id = Request::instance()->param('id',0);
        Db::name('merchant')->delete(['id'=>$id]);
        $this->success('delete merchant success');
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
        $param['price'] = round(session('merchant')['price']*$param['amount'],2);
        //找到最优质的的上游供货商
        $info = Db::name('supplier')->where(['status'=>'online'])->order('price desc')->field('id,price')->find();
        //Undelivered
        $param['pgw_id'] = $info['id'];
        $param['pgw_price'] = round($info['price']*$param['amount'],2);

        $model = new Order();
        $id = $model->store($param);
        if(empty($id)){
            $this->error('create order failed');
        }
        $this->success('create success');
    }



}
