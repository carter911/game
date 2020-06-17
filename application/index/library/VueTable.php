<?php
/**
 * Created by PhpStorm.
 * User: chenrj
 * Date: 2020-06-07
 * Time: 10:40
 */

namespace app\index\library;


use think\Db;
use think\Exception;
use think\Request;

trait VueTable
{
    // 指定表
    public $tableName='';
    // 指定关联的表
    public $relationTable = [];
    public $indexUrl='';
    public $updateUrl='';
    public $addUrl='';
    public $delUrl='';
    public $router='';
    public static $option_list =1;
    public static $option_save =2;
    public static $option_update =3;
    public static $option_delete =4;

    public function __construct()
    {

//        if(empty($this->table) || empty($this->indexUrl)){
//            retData([],500,'请指定要查询的表或者要获取的列表');
//        }
    }

    /**
     * 列表展示页
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $isJson = $request->get('json',0);
        $tableOption = $this->tableConfig();
        if(is_array($tableOption) || is_object($tableOption)){
            $tableOption = json_encode($tableOption);
        }
        $admin_user = session('admin_user');
        $this->assign('tableOption',$tableOption);
        $this->assign('indexUrl',$this->indexUrl);
        $this->assign('addUrl',$this->addUrl);
        $this->assign('updateUrl',$this->updateUrl);
        $this->assign('delUrl',$this->delUrl);
        $this->assign('router',$this->router);
        $this->assign('admin_user',$admin_user);

        if($isJson){
            echo $tableOption;die;
        }
        return $this->fetch($this->template);
    }


    public function tableConfig()
    {
       return [];
    }

    /**
     * 获取列表
     */
    public function get_list()
    {
        try{
            list($where, $sort, $order,  $page,$join,$filed) = $this->buildParams(self::$option_list);

            $count = Db::name($this->tableName)
                ->join($join)
                ->where($where)->count();
            $list = Db::name($this->tableName)->field($filed)->join($join)->where($where)->order($sort, $order)->limit($page->offset, $page->pageSize)->select();
            $this->formatList($list);
            $page->total = $count;

            retData(['list'=>$list,'count'=>$count,'page'=>$page,]);
        }catch (Exception $e){
            echo $e->getMessage();
            retData([],500,'系统在开小差 请稍后重试');
        }
    }


    /**
     * 更新数据
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public function store()
    {
        list($data,$where) = $this->buildParams(self::$option_update);

        //防止条件为空批量更新造成异常
        if(empty($where)){
            retData([],500,'系统在开小差 请稍后重试');
        }
        $result = Db::name($this->tableName)->where($where)->update($data);
        if(empty($result)){
            retData([],500,'系统在开小差 请稍后重试');
        }
        $this->optionExt($data,self::$option_delete);
        retData([],200,'更新成功');
    }

    /**
     * 新增数据
     */
    public function save()
    {
        list($data) = $this->buildParams(self::$option_save);
        $result = Db::name($this->tableName)->insert($data);
        if(empty($result)){
            retData([],500,'系统在开小差 请稍后重试');
        }
        $this->optionExt($data,self::$option_delete);
        retData([],200,'更新成功');
    }


    /**
     * 删除数据
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        list($where) = $this->buildParams(self::$option_delete);
        if(empty($where) && empty($where['id'])){
            retData([],403,'系统在开小差 请稍后重试');
        }
        $result = Db::name($this->tableName)->where($where)->delete();
        if(empty($result)){
            retData([],500,'系统在开小差 请稍后重试');
        }
        $this->optionExt($where,self::$option_delete);
        retData([],200,'删除成功');
    }

    /**
     * 扩展其他操作 比如获取列表时标记阅读数量 删除分类的同时删除子分类
     * @param $data
     * @param $type
     * @return mixed
     */
    public function optionExt($data,$type)
    {
        return $data;
    }

    /**
     * 用于格式化列表 可以在自己的方法中去使用该方法去格式化当前的数据
     * @param $list
     * @return mixed
     */
    public function formatList($list)
    {

        return $list;
    }

    /**
     * 格式化
     * @param $request
     * @param int $type
     * @return array
     */
    public function buildParams($type=1)
    {

        if(self::$option_list== $type){ //获取列表
            $param= Request::instance()->param();
            $where = [];
            $sort = 'id';
            $order = 'desc';
            $join = [];
            $filed = "`{$this->tableName}`.*";
            $page ="";

//            if(isset($param['page'])){
//                $page = json_decode($param['page']);
//
//            }
            $page = json_decode($param['page']);
            $page->pageSize = $page->pageSize?$page->pageSize:10;
            $page->currentPage = $page->currentPage?$page->currentPage:1;
            $page->offset = $page->pageSize*($page->currentPage-1);

            if(isset($param['search'])){
                $where = json_decode($param['search'],true);
            }

            if($this->relationTable){
                foreach ($this->relationTable as $key =>$val){
                    $join[] = [$val[0],$val[1],isset($val[2])?$val[2]:'LEFT'];
                    if(!empty($val[3])){
                        $filed .= ", ".$val[3];
                    }
                }
            }
            return $this->paramsUpdate([$where, $sort, $order, $page,$join,$filed], $type);
        }else if(self::$option_save==$type){// 新增
            $params = Request::instance()->param();
            $data = $params['params'];

            $data['create_time'] = date("Y-m-d H:i:s",time());
            $data['update_time'] = date("Y-m-d H:i:s",time());
            unset($data['id']);
            $data = $this->filedBySql($data);
            return $this->paramsUpdate([$data], $type);
        }else if(self::$option_update == $type){// 更新
            $params = Request::instance()->param();
            $data = $params['params'];
            $data['update_time'] = date("Y-m-d H:i:s",time());
            $data = $this->filedBySql($data);

            $where = ['id'=>$data['id']];
            return $this->paramsUpdate([$data,$where], $type);
        }else if(self::$option_delete == $type){// 删除
            $params = Request::instance()->param();
            $where['id'] = $params['id'];
            return $this->paramsUpdate([$where], $type);
        }else{
            return [];
        }

    }

    public function paramsUpdate($data,$type)
    {
        return $data;
    }

    public function filedBySql($data)
    {
        $tableFiled = Db::name($this->tableName)->getTableFields();
        foreach ($data as $key =>$val){
            if(!in_array($key,$tableFiled)){
                unset($data[$key]);
            }
            if(empty($val)){
                unset($data[$key]);
            }
            if(is_array($val)){
                $data[$key] = explode(",",$val);
            }
        }
        return $data;
    }
}
