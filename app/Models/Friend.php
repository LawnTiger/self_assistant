<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    public static function getFriends($user_id)
    {
//        self::whereUser_id($user_id)->whereStatus(1)->whereIs_delete(0)->get();
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
}
