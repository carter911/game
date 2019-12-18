<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use writethesky\PHPExcelReader\PHPExcelReader;

class Index extends Base
{
    use Table;
    use VueTable;
    public function __construct()
    {
        parent::__construct();
        $this->model = 'order';
    }
    public $searchField =['id','merchant_id',];
    public $showField =[];
    public $ignoreList = [];

    public function index()
    {
        return $this->fetch('index');

    }

    public function indexLog1265434568()
    {
        return retData();
        return $this->fetch('index');
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
