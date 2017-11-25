<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moment extends Model
{
    protected $hidden = ['updated_at'];

    public function setPicturesAttribute($value)
    {
        $this->attributes['pictures'] = implode(',', $value);
    }
}
