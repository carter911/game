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
            ->field("sum(price) as merchant_price,sum(pgw_price) as pgw_price,sum(amount) as amount,pgw_id,merchant_id,FROM_UNIXTIME(create_at,'%Y-%m-%d') days")
            ->group("pgw_id,merchant_id,FROM_UNIXTIME(create_at,'%Y-%m-%d')")->order("create_at desc")
            ->paginate(20);
        $this->assign('list', $list);
        $this->assign('search', $search);
        return $this->fetch('index');
    }


}
