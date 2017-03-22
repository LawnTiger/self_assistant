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

    public function resetpwd(Request $request)
    {
        $result = User::resetpwd($request->user()->id, $request->new_word, $request->old_word);
        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

    public function profiles()
    {

    }
}
