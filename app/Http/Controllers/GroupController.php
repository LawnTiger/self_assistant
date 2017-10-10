<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $group = GroupMember::get_list(\Auth::id());
        return response()->json($group);
    }

    public function store(Request $request)
    {
        $group = Group::create(['name' => $request->name]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => \Auth::id()]);
    }

    public function update(Request $request)
    {
        $user = User::find($request->user_id);
        if (empty($user)) {
            return 'no this user';
        }
        $input = $request->only(['group_id', 'user_id']);
        $group = GroupMember::where($input)->count();
        if ($group > 0) {
            return 'already in group';
        }
        GroupMember::create($input);
        return 'ok';
    }

    public function show($id)
    {

    }
}
