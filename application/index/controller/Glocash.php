<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use tp5redis\Redis;
use writethesky\PHPExcelReader\PHPExcelReader;

class Glocash extends Controller
{
    public function index()
    {


        $email = [
            '1059265030@qq.com',
            '1640732098@qq.com',
            '2150805246@qq.com',
            '2350035998@qq.com',
            '2783230065@qq.com',
            '3091431815@qq.com',
            '51612549@qq.com',
            '530771730@qq.com',
            '745933354@qq.com',
            '814820608@qq.com',
            'admin@17track.net',
            'aidenxi@126.com',
            'alek.zhong@11bill.com',
            'alek@cropay.com',
            'bokrun@163.com',
            'dandan.zhang@joyfun.com',
            'dandan.zhang@zroad.me',
            'fengz9924@gmail.com',
            'gxiang158@hotmail.com',
            'hepeng5212000@163.com',
            'jake2019@qq.com',
            'johnson.hedoudou@gmail.com',
            'manygolds2you@gmail.com',
            'mmoak2015@gmail.com',
            'nir@u4n.com',
            'okaygoods@gmail.com',
            'pay@mmocs.com',
            'pay3c@hotmail.com',
            'payment@littleboss.com',
            'payment@ogpal.com',
            'richardtian@babyonlinedress.com',
            'risk@altercards.com',
            'services@qufbao.com',
            //'speed4game@hotmail.com',
            'support@knockclick.com',
            'teddy@cropay.com',
            'vivianhaoy@gmail.com',
            'wowgoblinbot@gmail.com',
            'wuhaidaichong@163.com',
            'xiehuihui@cropay.com',
            'yumengol@outlook.com',
            'zhangfj1000@126.com',
            'zhouchaofu66@foxmail.com',];
        error_reporting(0);
        $name = '2019-01_part0_40000';
        $name = '2019-01_part1_1934';
////
//        $name = '2019-02_part0_39738';
//        $name = '2019-03_part0_40000';
//        $name = '2019-03_part1_16431';
////
//        $name = '2019-04_part0_40000';
//        $name = '2019-04_part1_17276';
////
//        $name = '2019-05_part0_40000';
//        $name = '2019-05_part1_28022';
////
//
//        $name = '2019-06_part0_40000';
//        $name = '2019-06_part1_50000';
//        $name = '2019-06_part2_11092';
////
//        $name = '2019-07_part0_40000';
//        $name = '2019-07_part1_50000';
//        $name = '2019-07_part2_50000';
//        $name = '2019-07_part3_8544';
////
//        $name = '2019-08_part0_40000';
//        $name = '2019-08_part1_50000';
//        $name = '2019-08_part2_50000';
//        $name = '2019-08_part3_25197';
////
//        $name = '2019-09_part0_40000';
//        $name = '2019-09_part1_50000';
//        $name = '2019-09_part2_50000';
//        $name = '2019-09_part3_1453';
////
//        $name = '2019-10_part0_40000';
//        $name = '2019-10_part1_50000';
//        $name = '2019-10_part2_56';
////
//        $name = '2019-11_part0_40000';
//        $name = '2019-11_part1_50000';
//        $name = '2019-11_part2_13398';
////
//        $name = '2019-12_part0_40000';
//        $name = '2019-12_part1_50000';
//        $name = '2019-12_part2_29851';

        $reader = new PHPExcelReader('/Users/chenrj/Documents/gc2019/gc-balance_@glocash_' . $name . '.csv');
        $data = [];

        foreach ($reader as $key => $row) {

            if ($key == 0 || $row[3] != 'paid') {
                continue;
            }
//            if($key>10){
//                break;
//            }
            //dump($row);
            if (!in_array($row[15], ['Safecharge', 'Payvision', 'Worldpay'])) {
                echo '渠道不对'.$row[15].'--'.$row[0].'<br/>';
                continue;
            }
            if ($row[6] >= 0) {
                echo 'chargeback'.$row[6].'--'.$row[0].'<br/>';
                continue;
            }
            if(!in_array($row[1],$email)){
                echo '不在范围的邮箱'.$row[1].'--'.$row[0].'<br/>';
                continue;
            }
            $item = [
                'gcid' => $row[0],
                'email' => $row[1],
                'reason' => $row[3],
                'amount' => $row[4],
                'currency' => $row[10],
                'amount_LT' => $row[12],
                'create_time' => $row[14],
                'pgw' => $row[15],
                'conversion_fee' => $row[5],
                'fee' => $row[6],
                'vat' => $row[7],
                'net' => $row[8],
            ];
            $data[] = $item;                    // 循环行内数据
        }
        //dump($data);die;
        echo "共计插入" . count($data) . '条数据</br>';
        Db::name('balance_merchant_fee')->insertAll($data);
    }



    public function fee_count()
    {


        $email = [
            '1059265030@qq.com',
            '1640732098@qq.com',
            '2150805246@qq.com',
            '2350035998@qq.com',
            '2783230065@qq.com',
            '3091431815@qq.com',
            '51612549@qq.com',
            '530771730@qq.com',
            '745933354@qq.com',
            '814820608@qq.com',
            'admin@17track.net',
            'aidenxi@126.com',
            'alek.zhong@11bill.com',
            'alek@cropay.com',
            'bokrun@163.com',
            'dandan.zhang@joyfun.com',
            'dandan.zhang@zroad.me',
            'fengz9924@gmail.com',
            'gxiang158@hotmail.com',
            'hepeng5212000@163.com',
            'jake2019@qq.com',
            'johnson.hedoudou@gmail.com',
            'manygolds2you@gmail.com',
            'mmoak2015@gmail.com',
            'nir@u4n.com',
            'okaygoods@gmail.com',
            'pay@mmocs.com',
            'pay3c@hotmail.com',
            'payment@littleboss.com',
            'payment@ogpal.com',
            'richardtian@babyonlinedress.com',
            'risk@altercards.com',
            'services@qufbao.com',
            'speed4game@hotmail.com',
            'support@knockclick.com',
            'teddy@cropay.com',
            'vivianhaoy@gmail.com',
            'wowgoblinbot@gmail.com',
            'wuhaidaichong@163.com',
            'xiehuihui@cropay.com',
            'yumengol@outlook.com',
            'zhangfj1000@126.com',
            'zhouchaofu66@foxmail.com',];


        error_reporting(0);

        $name = [
            '2019-01_part0_40000',
            '2019-01_part1_1934',

            '2019-02_part0_39738',

            '2019-03_part0_40000',
            '2019-03_part1_16431',

            '2019-04_part0_40000',
            '2019-04_part1_17276',

            '2019-05_part0_40000',
            '2019-05_part1_28022',

            '2019-06_part0_40000',
            '2019-06_part1_50000',
            '2019-06_part2_11092',

            '2019-07_part0_40000',
            '2019-07_part1_50000',
            '2019-07_part2_50000',
            '2019-07_part3_8544',

            '2019-08_part0_40000',
            '2019-08_part1_50000',
            '2019-08_part2_50000',
            '2019-08_part3_25197',

            '2019-09_part0_40000',
            '2019-09_part1_50000',
            '2019-09_part2_50000',
            '2019-09_part3_1453',

            '2019-10_part0_40000',
            '2019-10_part1_50000',
            '2019-10_part2_56',

            '2019-11_part0_40000',
            '2019-11_part1_50000',
            '2019-11_part2_13398',

            '2019-12_part0_40000',
            '2019-12_part1_50000',
            '2019-12_part2_29851',


        ];


//        $name = '2019-01_part0_40000';
//        $name = '2019-01_part1_1934';
////
//        $name = '2019-02_part0_39738';
//        $name = '2019-03_part0_40000';
//        $name = '2019-03_part1_16431';
////
//        $name = '2019-04_part0_40000';
//        $name = '2019-04_part1_17276';
////
//        $name = '2019-05_part0_40000';
//        $name = '2019-05_part1_28022';
////
//
//        $name = '2019-06_part0_40000';
//        $name = '2019-06_part1_50000';
//        $name = '2019-06_part2_11092';
////
//        $name = '2019-07_part0_40000';
//        $name = '2019-07_part1_50000';
//        $name = '2019-07_part2_50000';
//        $name = '2019-07_part3_8544';
////
//        $name = '2019-08_part0_40000';
//        $name = '2019-08_part1_50000';
//        $name = '2019-08_part2_50000';
//        $name = '2019-08_part3_25197';
////
//        $name = '2019-09_part0_40000';
//        $name = '2019-09_part1_50000';
//        $name = '2019-09_part2_50000';
//        $name = '2019-09_part3_1453';
////
//        $name = '2019-10_part0_40000';
//        $name = '2019-10_part1_50000';
//        $name = '2019-10_part2_56';
////
//        $name = '2019-11_part0_40000';
//        $name = '2019-11_part1_50000';
//        $name = '2019-11_part2_13398';
////
//        $name = '2019-12_part0_40000';
//        $name = '2019-12_part1_50000';
//        $name = '2019-12_part2_29851';

        foreach ($name as $key =>$val) {
            echo $val."</br>";
            $reader = new PHPExcelReader('/Users/chenrj/Documents/gc2019/gc-balance_@glocash_' . $val . '.csv');
            $data = [];
            foreach ($reader as $key => $row) {

                if ($key == 0 || $row[3] != 'paid') {
                    //continue;
                }
//            if($key>10){
//                break;
//            }
                //dump($row);
                if (!in_array($row[15], ['Safecharge', 'Payvision', 'Worldpay'])) {
                    echo '渠道不对' . $row[15] . '--' . $row[0] . '<br/>';
                    continue;
                }
//                if ($row[6] >= 0) {
//                    echo 'chargeback' . $row[6] . '--' . $row[0] . '<br/>';
//                    continue;
//                }
                if (!in_array($row[1], $email)) {
                    echo '不在范围的邮箱' . $row[1] . '--' . $row[0] . '<br/>';
                    continue;
                }
                $item = [
                    'gcid' => $row[0],
                    'email' => $row[1],
                    'reason' => $row[3],
                    'amount' => $row[4],
                    'currency' => $row[10],
                    'amount_LT' => $row[12],
                    'create_time' => $row[14],
                    'pgw' => $row[15],
                    'conversion_fee' => $row[5],
                    'fee' => $row[6],
                    'vat' => $row[7],
                    'net' => $row[8],
                    'tag'=>$val,
                    'date'=> date("Ym",strtotime($row[14]))
                ];
                $data[] = $item;
                // 循环行内数据

                if($key%20000 == 0){
                    Db::name('balance_merchant_fee')->insertAll($data);
                    $data =[];
                    echo "共计插入" . count($data) . '条数据</br>';
                }
            }
            //dump($data);die;
            echo "共计插入" . count($data) . '条数据</br>';
            Db::name('balance_merchant_fee')->insertAll($data);
        }
    }


    public function query(Request $request)
    {
        $name = $request->param('tag','2019-01_part1_1934');
        $list = Db::name('balance_merchant')->where(['tag'=>$name])->select();
        foreach ($list as $key => $val){
            echo json_encode($val).'<br/>';
        }
    }


    public function delete()
    {
        $name = '2019-01_part1_1934';
        Db::name('balance_merchant')->where(['tag'=>$name])->delete();
    }
    public function count()
    {
        //['create_time'=>['egt','2019-07-01 00:00:00']]//,sum(amount_lt)
        $list = Db::name('balance_merchant')->field('count(`gcid`) as num,sum(fixed_amount)')->where(['create_time'=>['egt','2019-07-01 00:00:00']])->select();
        $list1 = Db::name('balance_merchant')->field('count(`gcid`) as num,sum(fixed_amount)')->where(['create_time'=>['lt','2019-07-01 00:00:00']])->select();
        //$list = Db::name('balance_merchant')->field('count(`gcid`) as num,sum(amount_lt)')->where([])->select();
        dump($list);

        dump($list1);
    }

    public function count_fr()
    {
        $list = Db::name('balance_merchant')->join('upload_fraud','balance_merchant.gcid=upload_fraud.gcid')->field('count(balance_merchant.`gcid`) as num,sum(fixed_amount)')->where(['create_time'=>['egt','2019-07-01 00:00:00']])->select();
        dump($list);
    }

    public function check(Request $request)
    {
     // echo "汇率获取失败<br/ >";
        $page = $request->param('page',1);
        $count = $request->param('count',20000);
        $start = ($page-1)*$count;
        $num = Db::name('balance_merchant')->count();
        echo '当前第'.$page.'页共计 '.ceil($num/$count).'页<br/>';
        $list = Db::name('balance_merchant')->limit($start,$count)->select();
        echo Db::name('balance_merchant')->getLastSql().'<br/>';
        foreach ($list as $key => $val){
            $c = $this->getTate($val['currency'],$val['create_time']);
            if($val['currency']  =='EUR'){
                $c =1.0000;
            }
            if(99999 ==$c){
                echo $val['currency']."汇率不存在<br/>";
                continue;
            }
            echo $val['id'].'--汇率--'.$val['currency'].'--'.$c."--开始同步<br/>";
            Db::name('balance_merchant')->where(['id'=>$val['id']])->update(['fixed_amount'=>$val['amount']/$c]);
        }

    }

    public function getTate($currency,$time)
    {

        $time = date("Y-m-d",strtotime($time));
        //$rate = Redis::hGet('rate',$time);

        $rate = cache($time);
        if(empty($rate)){
            $rate = Db::name('lt_rate')->where(['time'=>$time])->field('currency')->find();
            //Redis::hSet('rate',$time,$rate['currency']);
            $rate = $rate['currency'];
            cache($time,$rate);
        }
        $rate = json_decode($rate,true);
        if(empty($rate[$currency])){
            return 99999;
        }
        return $rate[$currency];

    }

//    public function cache()
//    {
//        $start = "2019-06-01";
//        $end = "2019-12-31";
//        echo $start.'--'.$end.'<br />';
//        $date = $this->getDateFromRange($start,$end);
//        foreach ($date as $time){
//           $this->getTate('usd',$time);
//        }
//    }

    public function getDateFromRange($startdate, $enddate){

        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);

        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;

        // 保存每天日期
        $date = array();

        for($i=0; $i<$days; $i++){
            $date[] = date('Y-m-d', $stimestamp+(86400*$i));
        }

        return $date;
    }
}
