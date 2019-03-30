<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolePrivilegeRelevance extends Model
{
    public $table = 'role_privilege_relevance';
    public $timestamps = false;

    //由(单个或多个）r_id获得多个p_id
    public static function getPriid($r_id)
    {
        if(is_array($r_id)){
            $pids = self::whereIn('r_id',$r_id)->get(['p_id'])->toArray();
        }else{
            $pids = self::where('r_id','=',$r_id)->get(['p_id'])->toArray();
        }
        $pids = array_column($pids,'p_id');
        $pids = array_unique($pids);
        return $pids;
    }
}

