<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GroupMember;
use App\Models\Group;
use App\Models\User;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $group = GroupMember::get_list($request->user()->id);
        return app('jResponse')->success($group);
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|max:255'
        ]);
        if ($validator->fails()) {
            return app('jResponse')->error(head(head(head($validator->errors()))));
        }

        $group = Group::create(['name' => $request->name]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => $request->user()->id]);
        return app('jResponse')->success(['id' => $group->id]);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (empty($user) || empty($request->group_id)) {
            return app('jResponse')->error('the id is invalid');
        }
        $input = $request->only(['group_id']);
        $input['user_id'] = $id;
        $group = GroupMember::where($input)->count();
        if ($group > 0) {
            return app('jResponse')->error('already in group');
        }
        GroupMember::create($input);
        return app('jResponse')->success();
    }

    public function destroy($id)
    {
        //
    }
}
