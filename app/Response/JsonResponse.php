<?php

namespace App\Response;

class JsonResponse
{
    public function success($data)
    {
        return ['status' => 1, 'data' => $data];
    }

    public function error($msg)
    {
        return ['status' => 0, 'msg' => $msg];
    }
}
