<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public static function getNotes($userId)
    {
        return self::where('user_id', '=', $userId)
                ->orderBy('created_at', 'desc')->get();
    }
}
