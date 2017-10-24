<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RegisterRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\ApiTokenRepository;

class RegisterController extends Controller
{
    public function index(RegisterRequest $request, ApiTokenRepository $token)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'api_token' => $token->genToken(),
        ]);

        return app('response')->success($user);
    }
}
