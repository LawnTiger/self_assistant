<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GroupMember;

class GroupController extends Controller
{
    public function index(Request $request)
    {
        $group = GroupMember::get_list($request->user()->id);
        return app('jResponse')->success($group);
    }

    public function store(Request $request)
    {
        //
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
