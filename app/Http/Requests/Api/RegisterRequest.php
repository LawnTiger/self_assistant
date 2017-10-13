<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'bail|required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ];
    }

    public function response(array $errors)
    {
        return new JsonResponse(['status' => '0', 'msg' => $errors], 422);
    }
}
