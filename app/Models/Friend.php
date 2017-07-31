<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public static function getFriends($user_id)
    {
//        self::whereUser_id($user_id)->whereStatus(1)->whereIs_delete(0)->get();
    }

    public static function addFriend($from_id, $add_id, $status = 0)
    {
        $instance = new static;
        $instance->user_id = $from_id;
        $instance->friend_id = $add_id;
        $instance->status = $status;
        $string = md5($from_id . $add_id . time() . random_bytes(5));
        $instance->chat_key = $string;
        return $instance->save();
    }

    public static function isAdd($from_id, $add_id)
    {
        return self::friends($from_id, $add_id)->where('status', 1)
            ->where('is_delete', 0)->count();
    }

    public function accept()
    {

    }

    public function scopeFriends($query, $from_id, $add_id)
    {
        $query->where('user_id', $from_id)->where('friend_id', $add_id);
    }
}
