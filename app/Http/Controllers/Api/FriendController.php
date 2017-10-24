<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\User;

class FriendController extends Controller
{
    public function index(Request $request)
    {
        $list = Friend::FriendsList($request->user()->id);
        return app('jResponse')->success($list);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
