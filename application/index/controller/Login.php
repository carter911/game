<?php
namespace app\index\controller;

use app\index\library\Column;
use app\index\library\form\Select;
use app\index\library\Grid;
use app\index\library\VueTable;
use think\Db;
use think\Request;

class Login extends AdminBase
{

    public function __construct()
    {
        parent::__construct();
        $this->template="login/index";
        $this->tableName = "f_user";
        $this->indexUrl ='/index/login/checkLogin';
    }

    public function checkLogin(Request $request)
    {
        $param = $request->param();
        $admin = Db::name('f_admin')->where(['user_name'=>$param['user_name']])->find();
        if(empty($admin)){
            retData([],500,'账号或者密码错误');
        }
        if(Admin::makePassword($param['password']) == $admin['password']){
            session('admin_user',$param);
            Db::name('f_admin')->where(['user_name'=>$param['user_name']])->update(['login_time'=>date("Y-m-d H:i:s",time())]);
            retData([],200,'登录成功');
        }else{
            retData([],500,'账号或者密码错误');
        }
    }

    public function logout()
    {
        session('admin_user',[]);
        $this->redirect('/index/login/index');
    }


}
