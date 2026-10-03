<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageGroupUser extends Model
{
    use HasFactory;

    public function groupMessage()
    {
        return $this->belongsTo('App\Models\MessageGroup', 'message_group_id');
    }
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'admin_user');
    }
    public function mobileuser()
    {
        return $this->belongsTo('App\Models\Mobileuser', 'user_id');
    }
}
