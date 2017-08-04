<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Friend;

class ChatController extends Controller
{
    public function getIndex()
    {
        return view('friend.chat');
    }
}
