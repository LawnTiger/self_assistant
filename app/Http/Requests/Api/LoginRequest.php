<?php

namespace App\Http\Requests\Api;

class LoginRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|min:6',
        ];
    }
}
