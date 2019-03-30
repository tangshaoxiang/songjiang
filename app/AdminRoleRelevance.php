<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminRoleRelevance extends Model
{
    public $table = 'admin_role_relevance';
    public $timestamps = false;

    //由a_id获得r_id
    public static function getRid($a_id)
    {
        $rids = self::whereIn('a_id',[$a_id])->get(['r_id'])->toArray();
        $rids = array_column($rids,'r_id');
//        格式：$rids = [0=>1];
        return $rids;
    }

}