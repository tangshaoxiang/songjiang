<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminPrivilege extends Model
{
    public $table = 'privilege';
    protected $primaryKey = 'p_id';
    public $timestamps = false;
    protected $guarded = ['_token'];  //黑名单，fillable 与 guarded 只限制了 create 方法，而不会限制 save

    //获取所有权限
    public static function getAllPri($is_show=0){
        //默认传参为0,为0取所有,不为零取字段is_show为1的显示
        if($is_show==1){
            return self::where(['is_show'=>1])->get();
        }else{
            return self::all();
        }
    }

    //获取当前用户拥有的权限
    public static function getPriInfo($r_id){
        $pids = RolePrivilegeRelevance::getPriid($r_id);

        return self::whereIn('p_id',$pids)->get()->toArray();
    }


    //获取权限树（无限极分类）
    public static function getPriTree($allPriInfo,$pid=0){
        $tree = [];
        foreach($allPriInfo as $key=>$val){
            if($val->pid == $pid){
                $son = self::getPriTree($allPriInfo,$val->p_id);
                if(!empty($son))$val['son'] = $son;
                $tree[] = $val;
            }
        }
        return $tree;
    }

    //获取权限树（无限极分类）
    public static function getPriTreeObject($allPriInfo,$pid=0){
        $tree = [];
        foreach($allPriInfo as $key=>$val){
            if($val->pid == $pid){
                $son = self::getPriTreeObject($allPriInfo,$val->p_id);
                if(!empty($son))$val->son = $son;
                $tree[] = $val;
            }
        }
        return $tree;
    }


    //权限层级关系,排序权限(也可以使用path)
    public static function  getOrder($nodes,$pid=0,$level=1)
    {
        //global $arr ; //定义全局变量
        static $arr = [];  //或者定义静态的变量
        foreach ($nodes as $key=>$val){
            if($val->pid == $pid){
                $val->level=$level;
                $arr[] = $val;
                self::getOrder($nodes,$val->p_id,$level+1);
            }
        }
        return $arr;
    }

    //根据角色权限中间表中传过来的所有权限id(数组),查出所对应的权限名,并组合成一个字符串
    public static function getPriname($r_id)
    {
        $priName = self::whereIn('p_id',RolePrivilegeRelevance::getPriid($r_id))->get(['p_name'])->toArray();
        $priName = array_column($priName,'p_name');
//        dd($priName);
        return implode(',',$priName);
    }
}