<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {

    }

    public function store(Request $request)
    {
        $group = Group::create(['name' => $request->name]);
        GroupMember::create(['group_id' => $group->id, 'user_id' => \Auth::id()]);
    }

    public function update()
    {

    }

    public function destroy()
    {

    }
}
