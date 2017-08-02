<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Friend;

class ChatController extends Controller
{
    public function getIndex(Request $request)
    {
        $user_id = \Auth::id();

        return view('friend.chat', compact('user_id'));
    }
}
