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

    public static function saveNote($input)
    {
        $note = new static;
        $note->title = $input['title'];
        $note->content = $input['content'];
        $note->user_id = $input['user_id'];
        $note->save();

        return $note;
    }
}
