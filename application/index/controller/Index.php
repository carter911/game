<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use writethesky\PHPExcelReader\PHPExcelReader;

class Index extends Base
{
    public function index()
    {
//        return $this->fetch('index');
        return retData();
    }

    public function indexLog1265434568()
    {
        return $this->fetch('index');
        return retData();
    }

    public function getList()
    {
        $search = Request::instance()->param('search',[]);
        $page = Request::instance()->param('page',[]);
        $search = json_decode($search,true);
        $page = json_decode($page,true);
        $where = [];
        if(!empty($search['supplier_id']) ){
            $where['pgw_id'] = trim($search['supplier_id']);
        }

        if(!empty($search['id']) ){
            $where['id'] = trim($search['id']);
        }

        if(!empty($search['order_id']) ){
            $order_id = explode("-",$search['order_id']);
            if(isset($order_id[1])){
                $where['id'] = trim($order_id[1]);
            }

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
        $page['pageSize'] = isset($page['pageSize'])?$page['pageSize']:20;
        $page['currentPage'] = isset($page['currentPage'])?$page['currentPage']:1;
        $pageStart = $page['pageSize']*($page['currentPage']-1);
        $list = Db::name('order')->where($where)->order('id desc')->limit($pageStart,$page['pageSize'])->select();
        $count = Db::name('order')->where($where)->count();
        $page['total']  =$count;


        $this->setData($list);
        $this->setPage($page);
        $column = [
            ['label'=> 'ID', 'prop'=> 'id', 'search'=>true,],
            ['label'=> 'amount', 'prop'=> 'amount', 'search'=>false,],
            ['label'=> 'backup1', 'prop'=> 'backup1', 'search'=>true,],
            ['label'=> 'backup2', 'prop'=> 'backup2', 'search'=>true,],
        ];
        $this->setColumn($column);
        //$this->setOption();
        return retData($this->table);

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
