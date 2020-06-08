<?php
namespace app\index\controller;

use jianyan\excel\Excel;
use think\captcha\Captcha;
use think\Controller;
use think\Db;
use think\Request;
use writethesky\PHPExcelReader\PHPExcelReader;

class Index extends Base
{
//    use Table;
//    use VueTable;
    public function __construct()
    {
        parent::__construct();
        $this->model = 'order';
    }
    public $searchField =['id','merchant_id',];
    public $showField =[];
    public $ignoreList = [];

    public function index1()
    {
        //new Captcha();
        return retData();

        return $this->fetch('index');

    }

    public function captcha()
    {
        $captcha = new Captcha();
        return $captcha->entry();
    }

    function check_verify($code){
        $captcha = new Captcha();
        return $captcha->check($code);
    }

    public function indexLog12654345681()
    {
        return retData();
        return $this->fetch('index');
        return retData();
    }



    public function index()
    {
        set_time_limit(0);
        error_reporting(0);

        $data = Excel::import('./email.xlsx', 2);
        $data = array_column($data,0);
        $user = [];
        foreach ($data as $key => $val){
            $user[$val] = [
                'email'=>$val,
                'order_num'=>0,
                'paid_num'=>0,
                'coupon_num'=>0,
            ];
        }
        unset($data);
        $reader1 = Excel::import('./test.xlsx', 2);
//                echo "<pre>";
//        print_r($reader1);
//        echo "</pre>";
//        die;
        foreach ($user as $key => $val){
            foreach ($reader1 as $k=> $v){

                if($key == $v[0]){

                    $user[$key]['order_num'] +=1;
                    if($v[2] =='付款'){
                        $user[$key]['paid_num'] +=1;
                    }

                    if(!empty($v[3])){
                        $user[$key]['coupon_num'] +=1;
                    }


                }
                //if($val == )
            }
        }
        $user = array_values($user);
        $header = [
            ['email', 'email', 'text'],
            ['order_num', 'order_num'], // 规则不填默认text
            ['paid_num', 'paid_num', 'text'],
            ['coupon_num', 'coupon_num', 'text'],
        ];
        Excel::exportCsvData($user, $header);
        die;
        $this->assign('list',$user);
        return $this->fetch('index');
        unset($reader1);
        echo "<pre>";
        print_r($user);
        echo "</pre>";
        die;
        unset($data['日']);
        $customer = [];
        foreach ($data as $key => $val){
            $total = 0;
            $user_list = [];

            foreach ($val as $k=>$u){
                $total++;
                if(isset($user_list[$u[0]])){
                    $user_list[$u[0]] +=1;
                }else{
                    $user_list[$u[0]] = 1;
                }
            }
            $user_total = count($user_list);
            if($user_list>0){
                $avg = round($total/$user_total,2);
            }else{
                $avg =0;
            }

            $customer[$key] = ['user_num'=>$user_total,'total'=>$total,'avg'=>$avg,'day'=>$key];
        }
        $this->assign('list',$customer);
        return $this->fetch('index');
        //dump($customer);
    }
}
