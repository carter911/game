<?php
namespace app\supplier\controller;

use think\Db;
use think\Request;

class Index extends Base
{

    public function __construct()
    {
        parent::__construct();
        $supplier = session('supplier_id');
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
        $info = Db::name('supplier')->where(['name'=>$param['user_name']])->find();
        if(!$info){
            $this->error('account or password error');
        }
        if(hash('sha256',$param['password']) !=$info['password']){
            $this->error('account or password error');
        }
        session('supplier_id',$info['id']);
        session('supplier',$info);
        $this->success('login success','index');
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

        $supplier_id = session('supplier_id');
        $info = Db::name('supplier')->find($supplier_id);
        $list = Db::name('order')->where(['pgw_id'=>$supplier_id])->order('id desc')->paginate(20)->each(function($item, $key){
            $item['image'] = Request::instance()->domain().'/uploads/'.$item['image'];
            return $item;
        });

        $this->assign('list', $list);
        $this->assign('info', $info);
        return $this->fetch('index');
    }

    /**
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function store()
    {
        $supplier_id = session('supplier_id');
        $param = Request::instance()->only(['price','status']);
        $status = isset($param['status'])?'online':'offline';
        Db::name('supplier')->where(['id'=>$supplier_id])->update(['price'=>$param['price'],'status'=>$status,'update_at'=>time()]);
        $this->success('success');
    }

    public function status()
    {
        $file = request()->file('image');
        $image = "";
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                $image = $info->getSaveName();
            }else{
                // 上传失败获取错误信息
                //echo $file->getError();
            }
        }
        $supplier_id = session('supplier_id');
        $param = Request::instance()->only(['id','status']);
        if(empty($param['id'])){
            $this->error('error');
        }
        Db::name('Order')->where(['pgw_id'=>$supplier_id,'id'=>['in',$param['id']]])->update(['status'=>$param['status'],'image'=>$image,'update_at'=>time()]);
        $this->success('success');
    }

}
