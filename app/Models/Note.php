<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public static function getNotes($userId, $id = NULL)
    {
        $notes = self::where('user_id', $userId);
        if (! empty($id)) {
            $notes = $notes->where('id', $id);
        }

        return $notes->orderBy('created_at', 'desc')->get();
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

    public static function updateNote($id, $input)
    {
        $note = new static;

        return $note->where(compact('id'))->update($input);
    }
}
