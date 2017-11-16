<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Qiniu\Auth;

class DataController extends Controller
{
    public function qiniu_token()
    {
        $accessKey = \Config::get('qiniu.access_key');
        $secretKey = \Config::get('qiniu.secret_key');
        $bucket = \Config::get('qiniu.bucket');

        $auth = new Auth($accessKey, $secretKey);
        $token = $auth->uploadToken($bucket);

        return app('jResponse')->success(['token' => $token]);
    }
}
