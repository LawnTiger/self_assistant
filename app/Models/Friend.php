<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Friend extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];


    public static function friendsList($user_id)
    {
        $friends1 = self::where('user_id', $user_id)
            ->leftjoin('users', 'users.id', '=', 'friends.friend_id')
            ->where('status', 1)->select('friends.id', 'email', 'name', 'chat_key')->get();
        $friends2 = self::where('friend_id', $user_id)
            ->leftjoin('users', 'users.id', '=', 'friends.user_id')
            ->where('status', 1)->select('friends.id', 'email', 'name', 'chat_key')->get();
        return $friends1->merge($friends2);
    }

    public static function addList($user_id)
    {
        return self::leftJoin('users', 'users.id', '=', 'friends.user_id')
            ->select('name', 'email', 'users.id as adder_id')
            ->where('friend_id', $user_id)
            ->where('status', 0)->get();
    }

    public static function addFriend($from_id, $add_id)
    {
        $instance = new static;
        $instance->user_id = $from_id;
        $instance->friend_id = $add_id;
        $string = md5($from_id . $add_id . time() . random_bytes(5));
        $instance->chat_key = $string;
        $instance->save();
    }

    public static function isAdd($from_id, $add_id)
    {
        return self::friends($from_id, $add_id)->first();
    }

    public static function accept($friend_id, $accept_id, $type)
    {
        return self::where('user_id', $accept_id)
            ->where('friend_id', $friend_id)
            ->update(['status' => $type]);
    }

    public function scopeFriends($query, $id1, $id2)
    {
        $query->where(function ($q) use ($id1, $id2) {
            $q->where('user_id', $id1)->where('friend_id', $id2);
        })->orWhere(function ($q) use ($id1, $id2) {
            $q->where('user_id', $id2)->where('friend_id', $id1);
        });
    }
}
