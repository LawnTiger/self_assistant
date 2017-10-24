<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\LoginRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\ApiTokenRepository;

class LoginController extends Controller
{
    public function index(LoginRequest $request, ApiTokenRepository $token)
    {
        $user = User::where('email', $request->email)->first();
        if (! empty($user)) {
            $match = \Hash::check($request->password, $user->password);
            if ($match) {
                $user->api_token = $token->genToken();
                $user->save();
                return app('jResponse')->success($user);
            }
        }

        return app('jResponse')->error('email or password error');
    }
}
