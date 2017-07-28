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
        $add_id = User::whereEmail($request->email)->value('id');
        if (! empty($add_id)) {
            $result = Friend::addFriend(\Auth::id(), $add_id);
        } else {
            $result = -1;
        }

        return response()->json(['status' => $result]);
    }
}
