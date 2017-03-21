<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ResetPwdRequest;
use Hash;

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

    public function respwd()
    {
        print_r($request->all());
        echo encrypt($request->new_word).'<br />';
        echo bcrypt($request->new_word).'<br />';
        echo Hash::make($request->new_word);
    }

    public function profiles()
    {

    }
}
