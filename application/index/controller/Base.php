<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;
use writethesky\PHPExcelReader\PHPExcelReader;

class Base extends Controller
{

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
