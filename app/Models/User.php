<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function resetpwd($id, $oldWord, $newWord)
    {
        $user = self::find($id);
        $result = Hash::check($oldWord, $user->password);
        if ($result === true) {
            $user->password = Hash::make($newWord);
            $user->save();
            return true;
        } else {
            return false;
        }
    }
}
