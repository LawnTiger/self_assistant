<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function getIndex()
    {
        return view('admin.login');
    }

    public function postIndex(Request $request)
    {
        $validate = \Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ]);
        if ($validate->fails()) {
            $errors = $validate->errors();
            return redirect()->back()->withErrors($errors)->withInput();
        }

        $result = \Auth::guard('admin')->attempt([
            'name' => $request->name,
            'password' => $request->password
        ]);
        if ($result) {
            return redirect()->action('Admin\UserController@index');
        }

        return redirect()->back()->withErrors(['password' => '用户名或密码输入错误']);
    }

    public function out()
    {
        \Auth::logout();

        return redirect()->action('Admin\LoginController@getIndex');
    }
}
