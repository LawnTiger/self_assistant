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
            ->leftjoin('users', 'users.id', '=', 'friends.friend_id')->where('status', 1)
            ->select('friends.id', 'friends.friend_id', 'email', 'name', 'chat_key')->get();
        $friends2 = self::where('friend_id', $user_id)
            ->leftjoin('users', 'users.id', '=', 'friends.user_id')->where('status', 1)
            ->select('friends.id', 'friends.user_id as friend_id', 'email', 'name', 'chat_key')->get();
        $friends2->map(function ($item, $key) use($friends1) {
            // prevent from repeated
            if (! $friends1->contains($item)) {
                $friends1->push($item);
            }
        });
        return $friends1;
    }

    public static function addList($user_id)
    {
        return self::leftJoin('users', 'users.id', '=', 'friends.user_id')
            ->select('friends.id', 'users.id as friend_id', 'email', 'name')
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
        return $instance->id;
    }

    public static function isAdd($from_id, $add_id)
    {
        return self::friends($from_id, $add_id)->orderBy('id', 'desc')->first();
    }

    public static function accept($friend_id, $accept_id, $type)
    {
        $friend = self::where('user_id', $accept_id)->where('friend_id', $friend_id)
            ->where('status', 0)->update(['status' => $type]);
    }

    public static function eachIds($key)
    {
        return self::where('chat_key', $key)->where('status', 1)
            ->select('user_id', 'friend_id')->first();
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
