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

    public function __construct()
    {
        if(empty($this->table)){
            retData([],500,'请指定要查询的表');
        }

    }

    // 指定表
    public $table='';
    // 指定关联的表
    public $relationTable = [];

    /**
     *
     * @return mixed
     */
    public function index()
    {
        $tableOption = $this->tableConfig();
        if(is_array($tableOption)){
            $tableOption = json_encode($tableOption);
        }
        $this->assign('tableOption',$tableOption);
        return $this->fetch('index');
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
            list($where, $sort, $order, $offset, $limit,$join,$filed) = $this->buildParams(1);
            $count = Db::name($this->table)
                ->join($join)
                ->where($where)->count();
            $list = Db::name($this->table)->field($filed)->join($join)->where($where)->order($sort, $order)->limit($offset, $limit)->select();
            $this->formatList($list);
            retData(['list'=>$list,'count'=>$count]);
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
        list($where,$data) = $this->buildParams(3);

        //防止条件为空批量更新造成异常
        if(empty($where)){
            retData([],500,'系统在开小差 请稍后重试');
        }
        $result = Db::name($this->table)->where($where)->update($data);
        if($result){
            retData([],500,'系统在开小差 请稍后重试');
        }

        retData([],200,'更新成功');
    }

    /**
     * 新增数据
     */
    public function save()
    {
        list($data) = $this->buildParams(2);
        $result = Db::name($this->table)->insert($data);
        if($result){
            retData([],500,'系统在开小差 请稍后重试');
        }
        retData([],200,'更新成功');
    }


    /**
     * 删除数据
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    public function del()
    {
        list($data) = $this->buildParams(4);
        $result = Db::name($this->table)->delete($data);
        if($result){
            retData([],500,'系统在开小差 请稍后重试');
        }
        retData([],200,'删除成功');
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

        if(1== $type){ //获取列表
            $get = Request::instance()->get();
            $where = [];
            $sort = 'id';
            $order = 'desc';
            $offset = '0';
            $limit = 20;
            $join = [];
            $filed = "`{$this->table}`.*";
            if($this->relationTable){
                foreach ($this->relationTable as $key =>$val){
                    $join[] = [$val[0],$val[1],isset($val[2])?$val[2]:'LEFT'];
                    if(!empty($val[3])){
                        $filed .= ", ".$val[3];
                    }
                }
            }
            return [$where, $sort, $order, $offset, $limit,$join,$filed];
        }else if(2==$type){// 新增
            $data = [];
            return [$data];
        }else if(3 == $type){// 更新
            $data = [];
            $where = [];
            return [$where,$data];
        }else if(4 == $type){// 删除
            $where = [];
            return [$where];
        }else{
            return [];
        }

    }
}