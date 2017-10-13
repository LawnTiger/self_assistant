<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RegisterRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class RegisterController extends Controller
{
    public function index(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'api_token' => md5('hello'),
        ]);
        return ['status' => 1, 'data' => $user];
    }
}
