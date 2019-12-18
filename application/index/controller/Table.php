<?php
namespace app\index\controller;

use think\Db;
use think\Request;

trait Table
{
    public $model = '';
//    public $searchField =['id','merchant_id',];
//    public $showField =[];
//    public $ignoreList = [];


    public  $editHiddenField = ['id','create_at','update_at'];

    public $typeList = [
        'image'=>[
            'type'=>'upload',
            'accept'=>'array',
            'listType'=>'picture-img',
        ],
        'images'=>[
            'type'=>'upload',
            'accept'=>'array',
            'listType'=>'picture-card',
        ],
        'time'=>[
            'type'=>'date',
        ],
        'at'=>[
            'type'=>'date',
        ],
        'password'=>[
            'type'=>'password',
        ],
        'text'=>[
            'type'=>'textarea',
        ],
        'content'=>[
            'type'=>'textarea',
        ],
        'list'=>[
            'type'=>'array',
        ],

    ];



    public function getList()
    {
        $search = Request::instance()->param('search',[]);
        $page = Request::instance()->param('page',[]);
        $search = !empty($search)?json_decode($search,true):[];
        $page = !empty($search)?json_decode($page,true):[];
        $where = [];
        if($search){
            foreach ($search as $key=> $val){
                if(isset($val) && $val!=""){
                    $where[$key] = trim($val);
                }
            }
        }

        $where = $this->whereFormat($where);
        $page['pageSize'] = isset($page['pageSize'])?$page['pageSize']:20;
        $page['currentPage'] = isset($page['currentPage'])?$page['currentPage']:1;
        $pageStart = $page['pageSize']*($page['currentPage']-1);
        $list = Db::name($this->model)->where($where)->order('id desc')->limit($pageStart,$page['pageSize'])->select();
        $count = Db::name($this->model)->where($where)->count();
        $page['total']  =$count;
        $this->setData($list);
        $this->setPage($page);
        $tableInfo = Db::name($this->model)->getTableInfo();
        $column = [];
        foreach ($tableInfo['comment'] as $key =>$val){
            $search = false;
            $showForm = true;
            if(in_array($key,$this->searchField)){
                $search = true;
            }
            if(in_array($key,$this->ignoreList)){
                continue;
            }
            if(in_array($key,$this->editHiddenField)){
                $showForm = false;
            }

            $type['type'] = 'input';
            $keys = explode("_",$key);
            foreach ($keys as $item){
                if(in_array($item,array_keys($this->typeList))){
                    $type = $this->typeList[$item];
                }
            }

            $columnInfo = [
                'label'=> !empty($val)?$val:$key,
                'prop'=> $key,
                'search'=>$search,
                'editDisplay'=>$showForm,
                'addDisplay'=>$showForm,
            ];
            $columnInfo = array_merge($columnInfo,$type);
            $column[] = $columnInfo;
        }
        $this->setColumn($column);
        //$this->setOption();
        return retData($this->table);
    }

    public function whereFormat($where)
    {
        return $where;
    }

    public function store()
    {

    }


    public function delete()
    {

    }




    public  $table = [
        'page'=>[
            'pageSize'=> 10,
            'currentPage'=>1,
        ],
        'data'=>[],
        'option'=>[

        ],
        'where'=>[],

    ];


    public function __construct()
    {
        parent::__construct();

    }

    public function setColumn($column)
    {
        $this->table['option']['column'] = $column;
    }
    public function setOption($key,$val)
    {
        $this->table['option'][$key] = $val;
    }

    public function setPage(array $page)
    {
        $this->table['page'] = $page;
    }

    public function setData( array $data )
    {
        $this->table['data'] = $data;
    }
}
