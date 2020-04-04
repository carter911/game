<?php
namespace app\admin\controller;

use app\common\model\Merchant;
use app\common\model\Order;
use app\common\model\Supplier;
use think\Db;
use think\Log;
use think\Request;
use think\Validate;
use tp5redis\Redis;

class Transaction extends Base
{

    public function __construct()
    {
        parent::__construct();


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
        $where = ['status'=>"end"] ;
        $search['status'] = "end";
        if(!empty($search['supplier_id']) ){
            $where['pgw_id'] = trim($search['supplier_id']);
        }
        if(!empty($search['merchant_id']) ){
            $where['merchant_id'] = trim($search['merchant_id']);
        }

        $list = Db::name('order')
            ->where($where)->order('id desc')
            ->field("sum(price) as merchant_price,sum(pgw_price) as pgw_price,sum(amount) as amount,count(id) as num,pgw_id,merchant_id,FROM_UNIXTIME(create_at,'%Y-%m-%d') days")
            ->group("pgw_id,merchant_id,FROM_UNIXTIME(create_at,'%Y-%m-%d')")->order("create_at desc")
            ->paginate(20);


        //统计有多少交易中的
        $transferringCount = Db::name('order')
            ->where(['status'=>['in','transferring,new']])
            ->field("sum(price-pgw_price) as amount,count(id) as num")
            ->find();
        $endCount = Db::name('order')
            ->where(['status'=>['in','end'],'create_at'=>['egt',strtotime(date('Y-m-d'))]])
            ->field("sum(price-pgw_price) as amount,count(id) as num")
            ->group("status")
            ->find();


        $transferringCount['num'] = isset($transferringCount['num'])?intval($transferringCount['num']):0;
        $transferringCount['amount'] = isset($transferringCount['amount'])?floatval($transferringCount['amount']):0;
        $endCount['num'] = isset($endCount['num'])?intval($endCount['num']):0;
        $endCount['amount'] = isset($endCount['amount'])?floatval($endCount['amount']):0;


        $this->assign('transferringCount', $transferringCount);
        $this->assign('endCount', $endCount);

        $this->assign('list', $list);
        $this->assign('search', $search);
        return $this->fetch('index');
    }


}
