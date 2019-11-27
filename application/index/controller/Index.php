<?php
namespace app\index\controller;

use think\Controller;
use writethesky\PHPExcelReader\PHPExcelReader;

class Index extends Controller
{
    public function index()
    {
        return retData();
    }

    public function test()
    {
        error_reporting(0);
        $reader = new PHPExcelReader('./111.xlsx');
        $tatal = $reader->count();
        $data = [];
        foreach($reader as $key => $row){
            $data[$row[1]][] = $row;					// 循环行内数据
        }

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
