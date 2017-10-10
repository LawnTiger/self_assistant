<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'chat_messages';
    protected $guarded = ['id'];

    public static function message_list($id)
    {
        $list = self::where('to_id', $id)->where('is_read', 0)->with('user')->orderBy('from_id')->get();
        self::where('to_id', $id)->where('is_read', 0)->update(['is_read' => 1]);
        return $list;
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'from_id');
    }
}
