<?php
namespace app\index\controller;

use app\index\library\Column;
use app\index\library\form\Select;
use app\index\library\Grid;
use app\index\library\VueTable;
use think\Cache;
use think\Db;
use think\Request;

class Login extends AdminBase
{

    public static $max_error=3;
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
        $error_num = intval(Cache::get('error_num_'.$param['user_name']));
        if(self::$max_error<=$error_num){
            Cache::set('error_num_'.$param['user_name'],self::$max_error,30);
            retData([],403,'账号尝试失败过多 请稍后再试');
        }
        $admin = Db::name('f_admin')->where(['user_name'=>$param['user_name']])->find();
        if(empty($admin)){
            Cache::inc('error_num_'.$param['user_name']);
            retData([],500,'账号或者密码错误'.$error_num);
        }
        if(Admin::makePassword($param['password']) == $admin['password']){
            session('admin_user',$param);
            Db::name('f_admin')->where(['user_name'=>$param['user_name']])->update(['login_time'=>date("Y-m-d H:i:s",time())]);
            retData([],200,'登录成功');
        }else{
            Cache::inc('error_num_'.$param['user_name']);
            retData([],500,'账号或者密码错误'.$error_num);
        }
    }

    public function logout()
    {
        session('admin_user',[]);
        $this->redirect('/index/login/index');
    }


}
