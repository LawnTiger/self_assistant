<?php

namespace App\Repositories;

class ApiTokenRepository
{
    public function genToken()
    {
        return md5(openssl_random_pseudo_bytes(16).microtime());
    }
}
