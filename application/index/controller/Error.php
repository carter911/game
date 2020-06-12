<?php
namespace app\index\controller;

use app\index\library\Column;
use app\index\library\form\Select;
use app\index\library\Grid;
use app\index\library\VueTable;
use think\Controller;
use think\Db;
use think\Request;

class Error extends Controller
{
    public function index()
    {
        $this->redirect('/index/login/index');
    }

}
