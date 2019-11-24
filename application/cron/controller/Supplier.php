<?php

namespace app\cron\controller;

use app\common\gateway\Base;
use app\common\gateway\Ct;
use app\common\gateway\Gateway;
use app\common\logic\Pgw;
use think\Log;
use tp5redis\Redis;

class Supplier
{
    /**
     * 更新上游价格
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function autoSyncPrice()
    {
        echo "价格查询中<br/>";
        $model = new \app\common\model\Supplier();
        $list = $model->where(['is_auto' => 1])->select()->toArray();
        foreach ($list as $key => $val) {
            $gateway = "app\\common\\gateway\\".$val['pgw'];
            echo $gateway.'<br />';
            $pgw = new $gateway();
            $price = $pgw->getPrice();
            $status = [];
            foreach ($price as $k => $v){
                $status[$k] = 'offline';
                if($v!=999 && $v>0){
                    $status[$k] = 'online';
                }
            }
            echo "价格查询结果<br/>";
            dump($price);
            $res = $model->store(['status'=>$status,'price'=>$price],$val['id']);
        }
    }

    public function balance()
    {
        $model = new \app\common\model\Supplier();
        $list = $model->where(['is_auto' => 1])->select()->toArray();
        foreach ($list as $key => $val) {
            echo '更新渠道价格'.var_export($val).'<br />';
            $gateway = "app\\common\\gateway\\".$val['pgw'];
            $pgw = new $gateway();
            $price = $pgw->balance();
            dump($price);die;
            $res = $model->store(['currency'=>$price['currency'],'balance'=>$price['balance']],$val['id']);
            echo $model->getLastSql();
        }
    }

    /**
     * 获取上游渠道
     */
    public function sendToPayment()
    {
        $order = new \app\common\model\Order();
        $list = $order->where(['status'=>'Undelivered','pgw_payment'=>['neq','']])->order('id asc')->limit(1)->select();
        $list = $list->toArray();

        dump($list);
        foreach ($list as $key=> $val){
            if(empty($val['pgw_payment'])){
                return false;
            }

            $gateway = "app\\common\\gateway\\".$val['pgw_payment'];
            $pgw = new $gateway();
            $res = $pgw->newOrder($val);
            dump($res);
            unset($res['amount']);
            unset($res['platform']);
            Log::notice('交易结果pgw_order_id'.var_export($res,true));
            $res = $order->store($res,$val['id']);
            Log::info($order->getLastSql());
        }
    }

    public function getOrderStatus()
    {
        $order = new \app\common\model\Order();
        $list = $order->where(['status'=>'transferring'])->order('id asc')->limit(5)->select();
        $list = $list->toArray();
        foreach ($list as $key=> $val){
            echo "需要更新状态为".$val['pgw_order_id'].'上游网管'.$val['pgw_payment'].'<br/>';
            $gateway = "app\\common\\gateway\\".$val['pgw_payment'];
            $pgw = new $gateway();
            $res = $pgw->queryOrder($val);
            Log::notice($res);
            dump($res);
            $res = $order->store($res,$val['id']);
            Log::info($order->getLastSql());
        }
    }

    public function test()
    {

//        $url = 'https://mmoo.pl/u7buy/price';
//        $user = 'alex-b5c9c562541sa45gd357z';
//
//
//        $payload = array(
//            'user' => $user,
//        );
//        $open = json_encode($payload);
        $gateway = new Ct();
        $gateway->getPrice();

    }
}
