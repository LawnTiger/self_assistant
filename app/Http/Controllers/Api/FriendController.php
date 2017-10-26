<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\FriendUpdateRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        if (!empty($request->status) && $request->status == 'waiting') {
            $list = Friend::addList($request->user()->id);
        } else {
            $list = Friend::FriendsList($request->user()->id);
        }
        return app('jResponse')->success($list);
    }

    public function store(Request $request)
    {
        $user_id = $request->user()->id;
        $add_id = User::whereEmail($request->email)->value('id');

        if (!empty($add_id)) {
            $add = Friend::isAdd($user_id, $add_id);
            if (empty($add) || $add->status == 2) {
                Friend::addFriend($user_id, $add_id);
                return app('jResponse')->success(['add_id' => $add_id]);
            } elseif ($add->status == 0) {
                return app('jResponse')->success(['add_id' => $add_id]);
            } else {
                return app('jResponse')->error('该用户已添加');
            }
        } else {
            return app('jResponse')->error('未找到用户');
        }
    }

    public function update(FriendUpdateRequest $request, $id)
    {
        Friend::accept($request->user()->id, $id, $request->type);
        return app('jResponse')->success();
    }

    public function destroy(Request $request, $id)
    {
        $friend = Friend::where('user_id', $request->user()->id)
            ->where('friend_id', $id)->first();
        if ($friend) {
            $friend->delete();
            return app('jResponse')->success();
        } else {
            return app('jResponse')->error('the id is invalid');
        }
    }
}
