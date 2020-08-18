<?php
namespace app\admin\controller;

use \app\admin\library\Column;
use \app\admin\library\form\Select;
use \app\admin\library\Grid;
use \app\admin\library\VueTable;
use think\Controller;
use think\Db;
use think\Request;

class Error extends Controller
{
    public function index()
    {
        $this->redirect('/admin/login/index');
    }

}
