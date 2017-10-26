<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GroupMember;
use App\Models\Group;

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
        //
    }

    public function destroy($id)
    {
        //
    }
}
