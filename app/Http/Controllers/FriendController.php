<?php

namespace App\Http\Controllers;

use App\Http\Requests\FriendUpdateRequest;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\Friend;
use App\Models\User;

class FriendController extends Controller
{
    public function Index()
    {
        $user_id = \Auth::id();

        $friends = Friend::FriendsList($user_id);
        $adds = Friend::addList($user_id);
        $messages = Message::messageList($user_id);

        return view('friend.index', compact('friends', 'adds', 'messages'));
    }

    public function store(Request $request)
    {
        $user_id = \Auth::id();
        $add_id = User::whereEmail($request->email)->value('id');

        if (! empty($add_id)) {
            $add = Friend::isAdd($user_id, $add_id);
            if (empty($add) || $add->status == 2) {
                Friend::addFriend($user_id, $add_id);
                $result = 1;
                $message = $add_id;
            } elseif ($add->status == 0) {
                $result = 1;
                $message = $add->id;
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

    public function update(FriendUpdateRequest $request, $id)
    {
        Friend::accept(\Auth::id(), $id, $request->type);

        return response()->json(['status' => 1, 'message' => '成功']);
    }

    public function destroy($id)
    {
        Friend::destroy($id);
    }

    public function checkAdd()
    {
        $adds = Friend::addList(\Auth::id());
        return response()->json([$adds->toArray()]);
    }
}
