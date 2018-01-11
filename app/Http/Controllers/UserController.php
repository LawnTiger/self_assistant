<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ResetPwdRequest;
use App\Models\User;
use Auth;

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
        $result = User::resetpwd(Auth::id(), $request->old_word, $request->new_word);
        if ($result) {
            echo 'success';
            return redirect('user');
        } else {
            return redirect()->back()->withErrors('unmatch');
        }
    }

    public function profiles(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $user->name = $request->name;
        $user->save();

        return redirect('/');
    }
}
