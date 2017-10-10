<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = ['group_id', 'user_id'];

    public static function get_list($id)
    {
        return self::where('user_id', $id)->with('group')->get();
    }

    public function group()
    {
        return $this->belongsTo('App\Models\group', 'group_id');
    }
}
