<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\User;

class LoginController extends Controller
{
    public function index(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (! empty($user)) {
            $match = \Hash::check($request->password, $user->password);
            if ($match) {
                return app('jResponse')->success($user);
            }
        }

        return app('jResponse')->error('email or password error');
    }
}
