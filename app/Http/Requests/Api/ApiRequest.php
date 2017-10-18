<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class ApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function response(array $errors)
    {
        return new JsonResponse(['status' => '0', 'msg' => head(head($errors))], 422);
    }
}
