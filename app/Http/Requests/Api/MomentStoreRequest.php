<?php

namespace App\Http\Requests\Api;

class MomentStoreRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'contents' => 'required|max:255',
            'pictures' => 'array',
        ];
    }
}
