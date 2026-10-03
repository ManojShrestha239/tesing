<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageGroup extends Model
{
    use HasFactory;

    public function groupuser()
    {
        return $this->hasMany('App\Models\MessageGroupUser');
    }
    public function message()
    {
        return $this->hasMany('App\Models\Message', 'groupid');
    }
}
