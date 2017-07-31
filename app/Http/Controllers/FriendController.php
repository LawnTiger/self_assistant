<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friend;
use App\Models\User;

class FriendController extends Controller
{
    public function Index()
    {
        // 显示好友
        $friends = Friend::getFriends(\Auth::id());

        return view('friend.index', compact('friends'));
    }

    public function store(Request $request)
    {
        $user_id = \Auth::id();
        $add_id = User::whereEmail($request->email)->value('id');

        if (! empty($add_id)) {
            $add = Friend::isAdd($user_id, $add_id);
            if (empty($add)) {
                $result = Friend::addFriend($user_id, $add_id);
                $message = '请求成功';
            } else {
                $result = 2;
                $message = '该用户已添加';
            }
        } else {
            $result = -1;
            $message = '未找到用户';
        }

        return response()->json(['status' => $result, 'message' => $message]);
    }
}
