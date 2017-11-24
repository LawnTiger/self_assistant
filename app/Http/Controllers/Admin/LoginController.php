<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function getIndex()
    {
        if (\Auth::guard('admin')->check()) {
            return redirect()->action('Admin\UserController@index');
        }
        return view('admin.login');
    }

    public function postIndex(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
        ]);

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
        \Auth::guard('admin')->logout();

        return redirect()->action('Admin\LoginController@getIndex');
    }
}
