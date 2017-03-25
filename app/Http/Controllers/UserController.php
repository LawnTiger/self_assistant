<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ResetPwdRequest;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('user');
    }

    public function resetpwd(ResetPwdRequest $request)
    {
        $result = User::resetpwd($request->user()->id, $request->old_word, $request->new_word);
        if ($result) {
            echo 'success';
            return redirect('user');
        } else {
            return redirect()->back()->withErrors('unmatch');
        }
    }

    public function profiles(Request $request)
    {
        print_r($request->all());
        $user = new User();
        $user->name = $request->name;
        $user->save();
        echo 'success';
    }
}
