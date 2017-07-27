<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Friend;

class FriendController extends Controller
{
    public function Index()
    {
        // 显示好友
        $friends = Friend::getFriends(\Auth::id());

        return view('chat.index', compact('friends'));
    }
}
